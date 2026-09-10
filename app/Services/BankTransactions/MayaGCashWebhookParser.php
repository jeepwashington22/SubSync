<?php

namespace App\Services\BankTransactions;

use Illuminate\Support\Carbon;

/**
 * Parser for Philippine e-wallet webhook payloads (GCash / Maya). Both
 * providers emit a JSON array of transactions with slightly different field
 * names, so this class normalizes them into BankTransaction DTOs.
 */
class MayaGCashWebhookParser implements OpenBanking
{
    private string $providerKey;

    public function __construct(string $providerKey = 'gcash')
    {
        $this->providerKey = $providerKey;
    }

    public function provider(): string
    {
        return $this->providerKey;
    }

    public function syncTransactions(string $accountId, Carbon $from): array
    {
        // Pull-based sync is uncommon for wallet webhooks; consumers should
        // rely on parseWebhook() which is pushed in real time.
        return [];
    }

    public function parseWebhook(mixed $payload): array
    {
        $rows = [];

        // GCash webhook: { "transactions": [ { "txn_id", "description",
        // "amount", "currency", "timestamp" } ] }
        // Maya webhook:  [ { "transactionId", "narration",
        // "amount", "currency", "transactionDate" } ]
        $input = $payload;

        if (is_array($input) && isset($input['transactions'])) {
            $input = $input['transactions'];
        }

        if (! is_array($input)) {
            return [];
        }

        foreach ($input as $tx) {
            $id = $tx->txn_id ?? $tx->transactionId ?? null;
            $datetime = $tx->timestamp ?? $tx->transactionDate ?? null;
            $merchant = $tx->merchant ?? null;

            if ($id === null) {
                continue;
            }

            $rows[] = new BankTransaction(
                (string) $id,
                (string) ($tx->description ?? $tx->narration ?? ''),
                (float) ($tx->amount ?? 0),
                (string) ($tx->currency ?? 'PHP'),
                $datetime !== null ? Carbon::parse($datetime) : now(),
                $merchant !== null ? (string) $merchant : null,
            );
        }

        return $rows;
    }
}
