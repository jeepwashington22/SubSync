<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A raw transaction ingested from an Open Banking feed or webhook.
 */
class BankTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'bank_account_id',
        'provider',
        'provider_tx_id',
        'description',
        'merchant',
        'amount',
        'currency',
        'transaction_date',
        'is_subscription',
        'matched_subcription_id',
        'status',
        'raw',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
            'is_subscription' => 'bool',
            'raw' => 'json',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function matchedSubcription(): BelongsTo
    {
        return $this->belongsTo(Subcriptions::class, 'matched_subcription_id');
    }
}
