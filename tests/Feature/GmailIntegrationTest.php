<?php

namespace Tests\Feature;

use App\Models\Subcriptions;
use App\Models\User;
use App\Services\GmailReceiptParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GmailIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_route_redirects_to_google(): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'http://localhost/gmail/callback',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('gmail.redirect'));

        $response->assertRedirect();

        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_parser_extracts_service_name_and_price(): void
    {
        $parser = new GmailReceiptParser;

        $parsed = $parser->parse(
            'Netflix <info@netflix.com>',
            'Your Netflix receipt',
            'Your monthly plan payment of $15.49 was successful. Thanks for watching!'
        );

        $this->assertNotNull($parsed);
        $this->assertSame('Netflix', $parsed['name']);
        $this->assertSame(15.49, $parsed['price']);
        $this->assertSame('USD', $parsed['currency']);
    }

    public function test_parser_returns_null_for_unknown_senders_without_price(): void
    {
        $parser = new GmailReceiptParser;

        $this->assertNull($parser->parse(
            'random.sender@example.com',
            'Weekly newsletter',
            'No amounts in here'
        ));
    }

    public function test_scan_requires_connected_google_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('gmail.scan'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Connect Gmail first before scanning.');
    }

    public function test_scan_with_expired_token_redirects_with_error_without_duplicates(): void
    {
        $user = User::factory()->create([
            'google_access_token' => 'expired-token',
            'google_refresh_token' => 'refresh-token',
            'google_token_expires_at' => now()->subHour(),
        ]);

        Subcriptions::create([
            'user_id' => $user->id,
            'name' => 'Netflix',
            'price' => 9.99,
            'billing_cycle' => 'monthly',
            'billing_date' => now()->addMonth(),
            'category' => 'Streaming',
        ]);

        // Refresh against fake credentials fails; the user is redirected
        // with an error and no duplicate subscriptions are created.
        $response = $this->actingAs($user)->post(route('gmail.scan'));

        $response->assertRedirect(route('dashboard'));

        $this->assertSame(1, Subcriptions::where('user_id', $user->id)->count());
    }

    public function test_disconnect_clears_google_tokens(): void
    {
        $user = User::factory()->create([
            'google_access_token' => 'access-token',
            'google_refresh_token' => 'refresh-token',
            'google_token_expires_at' => now()->addHour(),
        ]);

        $response = $this->actingAs($user)->post(route('gmail.disconnect'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $user->refresh();

        $this->assertNull($user->google_access_token);
        $this->assertNull($user->google_refresh_token);
        $this->assertNull($user->google_token_expires_at);
        $this->assertFalse($user->hasGoogleConnected());
    }
}
