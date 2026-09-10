<?php

namespace App\Jobs;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Subcriptions;
use App\Models\User;
use App\Models\Workspace;
use App\Services\BankTransactions\BankTransaction as BankTransactionDto;
use App\Services\BankTransactions\MayaGCashWebhookParser;
use App\Services\BankTransactions\OpenBanking;
use App\Services\BankTransactions\PlaidMockOpenBanking;
use App\Services\BankTransactions\SubscriptionKeywordMatcher;
use App\Services\WorkspaceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Scans an Open Banking transaction feed for recurring subscription charges,
 * upserts the raw transactions, and either matches them to tracked
 * subscriptions or flags them as candidates for the user to review.
 */
class ProcessBankTransactionsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
        public ?int $bankAccountId = null,
        public ?array $pushedPayload = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);

        if ($user === null) {
            return;
        }

        $matcher = new SubscriptionKeywordMatcher;
        $provider = $this->resolveProvider();
        $transactions = $this->fetchTransactions($provider);

        $workspace = (new WorkspaceService)->personalWorkspace($user);

        foreach ($transactions as $tx) {
            $this->recordTransaction($user, $tx);
            $this->classifyTransaction($user, $workspace, $matcher, $tx);
        }
    }

    private function resolveProvider(): OpenBanking
    {
        $providerName = 'plaid';

        if ($this->bankAccountId !== null && BankAccount::where('id', $this->bankAccountId)->exists()) {
            $account = BankAccount::find($this->bankAccountId);
            $providerName = (string) $account->provider;
        }

        if ($providerName === 'maya' || $providerName === 'gcash') {
            return new MayaGCashWebhookParser($providerName);
        }

        return new PlaidMockOpenBanking;
    }

    private function fetchTransactions(OpenBanking $provider): array
    {
        if ($this->pushedPayload !== null) {
            // Pushed via webhook - normalize the raw payload.
            return $provider->parseWebhook($this->pushedPayload);
        }

        $accountId = 'default';

        if ($this->bankAccountId !== null && BankAccount::where('id', $this->bankAccountId)->exists()) {
            $account = BankAccount::find($this->bankAccountId);
            $accountId = (string) $account->provider_account_id;
        }

        // Look back 30 days.
        return $provider->syncTransactions($accountId, now()->addDays(-30));
    }

    private function recordTransaction(User $user, BankTransactionDto $tx): void
    {
        BankTransaction::updateOrCreate(
            [
                'provider' => 'mock',
                'provider_tx_id' => $tx->providerTxId,
            ],
            [
                'user_id' => $user->id,
                'bank_account_id' => $this->bankAccountId,
                'provider' => 'mock',
                'provider_tx_id' => $tx->providerTxId,
                'description' => $tx->description,
                'merchant' => $tx->merchant,
                'amount' => $tx->amount,
                'currency' => $tx->currency,
                'transaction_date' => $tx->date->toDateString(),
                'raw' => ['description' => $tx->description, 'amount' => $tx->amount],
            ],
        );
    }

    /**
     * Match a transaction to a known service. Resolution order:
     *  1. Reuse the merchant from an existing tracked subscription.
     *  2. Match against the keyword knowledge base.
     *  3. Leave it unclassified.
     */
    private function classifyTransaction(User $user, Workspace $workspace, SubscriptionKeywordMatcher $matcher, BankTransactionDto $tx): void
    {
        $serviceName = $matcher->match($tx->description);

        if ($serviceName === null) {
            return;
        }

        $existing = Subcriptions::where('workspace_id', $workspace->id)
            ->where('name', $serviceName)
            ->first();

        $status = $existing !== null ? 'matched' : 'candidate';
        $matchedSubcriptionId = $existing !== null ? $existing->id : null;

        BankTransaction::where('user_id', $user->id)
            ->where('provider_tx_id', $tx->providerTxId)
            ->update([
                'status' => $status,
                'is_subscription' => true,
                'matched_subcription_id' => $matchedSubcriptionId,
            ]);

        if ($existing === null) {
            $this->maybeProvisionSubscription($user, $workspace, $serviceName, $tx);
        }
    }

    /**
     * Auto-provision a subscription once a candidate charge appears twice
     * (evidence of recurrence). Flags the workspace dashboard accordingly.
     */
    private function maybeProvisionSubscription(User $user, Workspace $workspace, string $serviceName, BankTransactionDto $tx): void
    {
        $recurringCount = BankTransaction::where('user_id', $user->id)
            ->where('status', 'candidate')
            ->where('description', 'like', '%'.$serviceName.'%')
            ->count();

        if ($recurringCount < 2) {
            return;
        }

        if (Subcriptions::where('workspace_id', $workspace->id)->where('name', $serviceName)->exists()) {
            return;
        }

        Subcriptions::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'added_by_user_id' => $user->id,
            'name' => $serviceName,
            'category' => 'Auto-synced from bank',
            'price' => $tx->amount,
            'billing_cycle' => 'monthly',
            'billing_date' => $tx->date->toDateString(),
        ]);

        Log::info('Subscription auto-provisioned from bank sync', [
            'user_id' => $user->id,
            'name' => $serviceName,
        ]);
    }
}
