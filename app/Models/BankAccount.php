<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A linked bank / e-wallet account (Plaid link, Maya, GCash).
 */
class BankAccount extends Model
{
    protected $fillable = ['user_id', 'provider', 'provider_account_id', 'display_name', 'institution', 'is_active', 'metadata'];

    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
            'metadata' => 'json',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class);
    }
}
