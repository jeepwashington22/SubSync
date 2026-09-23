<?php

namespace Tests\Feature;

use App\Models\Subcriptions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopbarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The shared topbar must render on every authenticated page, and the
     * notification bell must sit inside <header> (never below it).
     */
    public function test_topbar_contains_bell_and_profile_on_every_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $pages = [
            route('dashboard'),
            route('billing-history.index'),
            route('workspaces.index'),
            route('bank-accounts.index'),
            route('profile.edit'),
        ];

        foreach ($pages as $url) {
            $html = $this->actingAs($user)->get($url)->assertOk()->getContent();

            $headerEnd = strpos($html, '</header>');
            $this->assertNotFalse($headerEnd, "No topbar header found on {$url}");

            $bellPos = strpos($html, 'aria-label="Notifications"');
            $this->assertNotFalse($bellPos, "Notification bell missing on {$url}");
            $this->assertLessThan($headerEnd, $bellPos, "Notification bell renders outside the topbar on {$url}");

            $profilePos = strpos($html, 'aria-label="Account menu"');
            $this->assertNotFalse($profilePos, "Profile menu missing on {$url}");
            $this->assertLessThan($headerEnd, $profilePos, "Profile menu renders outside the topbar on {$url}");

            // The bell must appear exactly once (no orphaned duplicate)
            $this->assertSame(1, substr_count($html, 'aria-label="Notifications"'), "Duplicate bell on {$url}");
        }
    }

    public function test_bell_shows_upcoming_renewal_and_badge(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Subcriptions::create([
            'user_id' => $user->id,
            'name' => 'Netflix',
            'price' => 549,
            'billing_cycle' => 'monthly',
            'billing_date' => now()->addDays(5),
            'category' => 'entertainment',
        ]);

        $html = $this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('Netflix renews soon', $html);
        // Badge dot is rendered when there is something to review
        $this->assertStringContainsString('bg-orange-500 ring-2 ring-[#111214]', $html);
    }

    public function test_bell_shows_empty_state_without_renewals(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $html = $this->actingAs($user)->get(route('billing-history.index'))->assertOk()->getContent();

        $this->assertStringContainsString("You're all caught up", $html);
    }
}
