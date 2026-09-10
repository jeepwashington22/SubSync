<?php

namespace App\Services\BankTransactions;

use Illuminate\Support\Carbon;

/**
 * Read-only Open Banking implementation designed to be driven by fixture
 * (sample) data - useful for local development and deterministic tests before
 * wiring a live Plaid/GCash/Maya sandbox receipt.
 */
class PlaidMockOpenBanking implements OpenBanking
{
    /**
     * Sample ledger used to seed the sync. Real integrations would hit the
     * Plaid Transactions API and map each transaction into a DTO.
     *
     * @var array<int, array>
     */
    private array $sample = [
        ['NETFLIX.COM MONTHLY', 549.00, 'PHP'],
        ['SPOTIFY PREMIUM', 129.00, 'PHP'],
        ['GCASH PAY NETFLIX', 549.00, 'PHP'],
        ['VERCEL HOSTING', 20.00, 'USD'],
        ['AMAZON PRIME VIDEO', 149.00, 'PHP'],
        // Deliberately not a subscription (one-off purchase).
        ['MCDONALDS MANILA', 320.00, 'PHP'],
    ];

    public function provider(): string
    {
        return 'plaid';
    }

    public function syncTransactions(string $accountId, Carbon $from): array
    {
        $transactions = [];

        foreach ($this->sample as $index => $row) {
            $transactions[] = new BankTransaction(
                'mock-'.$index.'-'.$accountId,
                $row[0],
                (float) $row[1],
                $row[2],
                $from->addDays($index),
            );
        }

        return $transactions;
    }

    public function parseWebhook(mixed $payload): array
    {
        // Plaid webhooks (SYNC_UPDATES_AVAILABLE) carry no transaction rows;
        // the consumer reacts by calling syncTransactions().
        return [];
    }
}
