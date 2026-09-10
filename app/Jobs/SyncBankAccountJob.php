<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Entry point triggered by an Open Banking webhook (e.g. Plaid
 * SYNC_UPDATES_AVAILABLE). Lightweight: forwards to the processing job so
 * webhooks return quickly and the work happens asynchronously/deduplicated.
 */
class SyncBankAccountJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $userId, public ?array $payload = null) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ProcessBankTransactionsJob::dispatch($this->userId, null, $this->payload);
    }
}
