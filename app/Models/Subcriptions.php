<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcriptions extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'added_by_user_id',
        'name',
        'category',
        'price',
        'split_share',
        'billing_cycle',
        'billing_date',
    ];

    protected function casts(): array
    {
        return [
            'billing_date' => 'date',
            'price' => 'decimal:2',
            'split_share' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    /** Historical billing amounts, newest first. */
    public function billingHistories(): HasMany
    {
        return $this->hasMany(BillingHistory::class, 'subcription_id')->orderBy('billed_at', 'desc');
    }

    /** Per-member cost splits within the shared workspace. */
    public function splits(): HasMany
    {
        return $this->hasMany(SubscriptionSplit::class, 'subcription_id');
    }
}
