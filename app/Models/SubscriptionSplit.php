<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Percentage cost split of a shared subscription across workspace users.
 */
class SubscriptionSplit extends Model
{
    protected $fillable = ['subcription_id', 'user_id', 'percent_share'];

    protected function casts(): array
    {
        return [
            'percent_share' => 'decimal:2',
        ];
    }

    public function subcription(): BelongsTo
    {
        return $this->belongsTo(Subcriptions::class, 'subcription_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
