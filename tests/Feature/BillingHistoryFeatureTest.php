<?php

namespace Tests\Feature;

use App\Models\BillingHistory;
use App\Models\Subcriptions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingHistoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_view_only_their_billing_history(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $subscription = $this->createSubscription($user, 'Personal hosting');
        $otherSubscription = $this->createSubscription($otherUser, 'Other hosting');

        BillingHistory::create([
            'subcription_id' => $subscription->id,
            'amount' => 249.00,
            'currency' => 'PHP',
            'billed_at' => '2026-09-01',
            'source' => 'manual',
        ]);
        BillingHistory::create([
            'subcription_id' => $otherSubscription->id,
            'amount' => 999.00,
            'currency' => 'PHP',
            'billed_at' => '2026-09-02',
            'source' => 'bank',
        ]);

        $response = $this->actingAs($user)->get(route('billing-history.index'));

        $response->assertSee('Personal hosting');
        $response->assertSee('PHP 249.00');
        $response->assertDontSee('Other hosting');
        $response->assertDontSee('PHP 999.00');
    }

    public function test_a_guest_is_redirected_to_login_from_billing_history(): void
    {
        $response = $this->get(route('billing-history.index'));

        $response->assertRedirect(route('login'));
    }

    private function createSubscription(User $user, string $name): Subcriptions
    {
        return Subcriptions::create([
            'user_id' => $user->id,
            'name' => $name,
            'price' => 249.00,
            'billing_cycle' => 'monthly',
            'billing_date' => '2026-09-15',
            'category' => 'Hosting',
        ]);
    }
}
