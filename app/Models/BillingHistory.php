<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single recorded billing amount for a subscription. The history is the
 * source of truth for price-hike (subscription creep) detection.
 */
class BillingHistory extends Model
{
    protected $fillable = ['subcription_id', 'amount', 'currency', 'billed_at', 'source'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'billed_at' => 'date',
        ];
    }

    public function subcription(): BelongsTo
    {
        return $this->belongsTo(Subcriptions::class, 'subcription_id');
    }
}
