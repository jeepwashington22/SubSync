<?php

namespace Tests\Feature;

use App\Models\Subcriptions;
use App\Models\User;
use App\Services\PriceHikeDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceHikeDetectorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_detects_a_price_hike_when_billing_history_has_two_records(): void
    {
        $user = User::factory()->create();

        $subscription = Subcriptions::create([
            'user_id' => $user->id,
            'workspace_id' => null,
            'added_by_user_id' => $user->id,
            'name' => 'Netflix Premium',
            'category' => 'Streaming',
            'price' => 499.00,
            'billing_cycle' => 'monthly',
            'billing_date' => now()->toDateString(),
        ]);

        $detector = new PriceHikeDetector;

        // Only one history record — no hike detectable yet.
        $detector->recordBilling($subscription, 'manual', 499.00);
        $this->assertNull($detector->forSubcription($subscription));

        // Second record at a higher price — hike detected.
        $detector->recordBilling($subscription, 'manual', 799.00);

        $hike = $detector->forSubcription($subscription);

        $this->assertNotNull($hike);
        $this->assertSame('Netflix Premium', $hike['subcription']->name);
        $this->assertEqualsWithDelta(499.00, $hike['previous_amount'], 0.01);
        $this->assertEqualsWithDelta(799.00, $hike['current_amount'], 0.01);
        $this->assertEqualsWithDelta(300.00, $hike['increase'], 0.01);
        $this->assertEqualsWithDelta(60.12, $hike['percent_change'], 0.01);
    }

    /** @test */
    public function it_returns_null_for_a_price_drop_or_same_price(): void
    {
        $user = User::factory()->create();

        $subscription = Subcriptions::create([
            'user_id' => $user->id,
            'workspace_id' => null,
            'added_by_user_id' => $user->id,
            'name' => 'Spotify Family',
            'category' => 'Streaming',
            'price' => 399.00,
            'billing_cycle' => 'monthly',
            'billing_date' => now()->toDateString(),
        ]);

        $detector = new PriceHikeDetector;

        $detector->recordBilling($subscription, 'manual', 499.00);
        $detector->recordBilling($subscription, 'manual', 399.00);

        $this->assertNull($detector->forSubcription($subscription));
    }

    /** @test */
    public function it_returns_null_for_a_tiny_price_change_below_threshold(): void
    {
        $user = User::factory()->create();

        $subscription = Subcriptions::create([
            'user_id' => $user->id,
            'workspace_id' => null,
            'added_by_user_id' => $user->id,
            'name' => 'Cloud Storage',
            'category' => 'Other',
            'price' => 100.00,
            'billing_cycle' => 'monthly',
            'billing_date' => now()->toDateString(),
        ]);

        $detector = new PriceHikeDetector;

        $detector->recordBilling($subscription, 'manual', 100.00);
        $detector->recordBilling($subscription, 'manual', 100.50);

        // A 0.5% change is below the 1% minimum threshold.
        $this->assertNull($detector->forSubcription($subscription));
    }

    /** @test */
    public function it_detects_hikes_across_multiple_subscriptions(): void
    {
        $user = User::factory()->create();

        $netflix = Subcriptions::create([
            'user_id' => $user->id,
            'workspace_id' => null,
            'added_by_user_id' => $user->id,
            'name' => 'Netflix',
            'category' => 'Streaming',
            'price' => 499.00,
            'billing_cycle' => 'monthly',
            'billing_date' => now()->toDateString(),
        ]);

        $vercel = Subcriptions::create([
            'user_id' => $user->id,
            'workspace_id' => null,
            'added_by_user_id' => $user->id,
            'name' => 'Vercel Pro',
            'category' => 'Hosting',
            'price' => 250.00,
            'billing_cycle' => 'monthly',
            'billing_date' => now()->toDateString(),
        ]);

        $detector = new PriceHikeDetector;

        $detector->recordBilling($netflix, 'manual', 499.00);
        $detector->recordBilling($netflix, 'manual', 699.00);

        // Vercel price didn't change — no hike.
        $detector->recordBilling($vercel, 'manual', 250.00);
        $detector->recordBilling($vercel, 'manual', 250.00);

        $hikes = $detector->forSubcriptions([$netflix, $vercel]);

        $this->assertCount(1, $hikes);
        $this->assertSame('Netflix', $hikes[0]['subcription']->name);
    }
}
