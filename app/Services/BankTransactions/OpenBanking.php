<?php

namespace App\Services\BankTransactions;

use Illuminate\Support\Carbon;

/**
 * Normalized transaction payload shared across all Open Banking providers.
 */
class BankTransaction
{
    public function __construct(
        public string providerTxId,
        public string description,
        public float amount,
        public string currency,
        public Carbon date,
        public ?string merchant = null,
    ) {
    }
}

/**
 * Provider-agnostic contract for Open Banking transaction feeds.
 * Implementations wrap Plaid, Maya/GCash webhooks, etc. and normalize the
 * payloads into BankTransaction DTOs.
 */
interface OpenBanking
{
    /** Provider key, e.g. "plaid", "maya", "gcash". */
    public function provider(): string;

    /**
     * Fetch a slice of transactions for a linked account.
     *
     * @param  string  $accountId  Provider account identifier.
     * @param  Carbon  $from       Inclusive start date.
     * @return array<int, BankTransaction>
     */
    public function syncTransactions(string $accountId, Carbon $from): array;

    /**
     * Parse an inbound webhook payload into zero or more transactions.
     *
     * @param  mixed  $payload
     * @return array<int, BankTransaction>
     */
    public function parseWebhook(mixed $payload): array;
}