<?php

namespace App\Http\Controllers;

use App\Jobs\SyncBankAccountJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receives Open Banking webhooks (Plaid, Maya, GCash). Verifies the shared
 * webhook secret, resolves the affected user, and queues the sync.
 */
class BankWebhookController extends Controller
{
    /**
     * Handle an inbound webhook payload.
     *
     * Expected JSON: { "provider": "plaid|maya|gcash", "user_id": 1,
     *                  "account_id": 12, "transactions": [ ... ] }
     * Header:        X-Webhook-Secret: <BANK_WEBHOOK_SECRET>
     */
    public function handle(Request $request): JsonResponse
    {
        $secret = (string) config('services.bank.webhook_secret');

        if ($secret !== '' && $request->header('X-Webhook-Secret') !== $secret) {
            Log::warning('Bank webhook rejected: bad secret');

            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'provider' => 'required|string|max:30',
            'user_id' => 'required|integer',
            'account_id' => 'nullable|integer',
            'transactions' => 'nullable|array',
        ]);

        SyncBankAccountJob::dispatch(
            (int) $validated['user_id'],
            $validated['account_id'] ?? null,
            $validated['transactions'] ?? null,
        );

        return response()->json(['received' => true]);
    }
}
