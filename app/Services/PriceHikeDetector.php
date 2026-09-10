<?php

namespace App\Services;

use App\Models\BillingHistory;
use App\Models\Subcriptions;

/**
 * Subscription-creep tracker. Compares a subscription's most recent recorded
 * billing amount against the previous historical record and reports any
 * increase.
 */
class PriceHikeDetector
{
    /**
     * The minimum percentage increase considered a "hike" (avoids flagging
     * tiny rounding differences).
     */
    private const MIN_HIKE_PERCENT = 1.0;

    /**
     * Detect a price hike for a single subscription.
     *
     * @return array{subcription: Subcriptions, previous_amount: float,
     *                current_amount: float, increase: float, percent_change: float}|null
     */
    public function forSubcription(Subcriptions $subcription): ?array
    {
        $histories = $subcription->billingHistories()->limit(2)->get();

        if ($histories->count() < 2) {
            return null;
        }

        $current = (float) $histories[0]['amount'];
        $previous = (float) $histories[1]['amount'];

        if ($previous <= 0) {
            return null;
        }

        $percentChange = (($current - $previous) / $previous) * 100;

        if ($current <= $previous || $percentChange < self::MIN_HIKE_PERCENT) {
            return null;
        }

        return [
            'subcription' => $subcription,
            'previous_amount' => $previous,
            'current_amount' => $current,
            'increase' => $current - $previous,
            'percent_change' => $percentChange,
        ];
    }

    /**
     * Detect hikes across a collection of subscriptions.
     *
     * @param  array<int, Subcriptions>  $subcriptions
     * @return array<int, array>
     */
    public function forSubcriptions(array $subcriptions): array
    {
        $hikes = [];

        foreach ($subcriptions as $subcription) {
            $result = $this->forSubcription($subcription);

            if ($result !== null) {
                $hikes[] = $result;
            }
        }

        return $hikes;
    }

    /**
     * Convenience: log a billing snapshot on subscription creation / update.
     * Always keep the current price in sync with the latest history row.
     */
    public function recordBilling(Subcriptions $subcription, string $source = 'manual', ?float $amount = null): BillingHistory
    {
        $amount = $amount ?? (float) $subcription->price;

        return BillingHistory::create([
            'subcription_id' => $subcription->id,
            'amount' => $amount,
            'currency' => 'USD',
            'billed_at' => $subcription->billing_date ?? now()->toDate(),
            'source' => $source,
        ]);
    }
}
