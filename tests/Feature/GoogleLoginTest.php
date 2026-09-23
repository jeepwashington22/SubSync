<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_sends_user_to_google(): void
    {
        config()->set('services.google_login.client_id', 'test-client-id');
        config()->set('services.google_login.client_secret', 'test-client-secret');
        config()->set('services.google_login.redirect', 'http://localhost:8000/auth/google/callback');

        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com/o/oauth2/auth', $response->getTargetUrl());
        $this->assertStringContainsString('client_id=test-client-id', $response->getTargetUrl());
    }

    public function test_callback_creates_new_user_and_logs_them_in(): void
    {
        $this->fakeGoogleUser(email: 'newuser@gmail.com');

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticated();

        $user = User::query()->where('email', 'newuser@gmail.com')->firstOrFail();
        $this->assertNull($user->password);
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame('google', $user->auth_provider);

        // Google-provider users are chained into the Gmail consent flow
        // on first login so their inbox can be scanned automatically.
        $response->assertRedirect(route('gmail.redirect'));
    }

    public function test_callback_links_existing_account_by_email(): void
    {
        $existing = User::factory()->unverified()->create([
            'email' => 'existing@gmail.com',
        ]);

        $this->fakeGoogleUser(email: 'existing@gmail.com');

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('gmail.redirect'));
        $this->assertAuthenticatedAs($existing);
        $this->assertNotNull($existing->refresh()->email_verified_at);
        $this->assertSame('google', $existing->refresh()->auth_provider);
        $this->assertSame(1, User::query()->where('email', 'existing@gmail.com')->count());
    }

    public function test_google_user_with_gmail_already_connected_goes_to_dashboard(): void
    {
        User::factory()->create([
            'email' => 'connected@gmail.com',
            'auth_provider' => 'google',
            'google_access_token' => 'existing-token',
        ]);

        $this->fakeGoogleUser(email: 'connected@gmail.com');

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();
    }

    public function test_callback_rejects_missing_email(): void
    {
        $this->fakeGoogleUser(email: null);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertSame(0, User::query()->count());
    }

    public function test_callback_redirects_back_to_login_when_google_fails(): void
    {
        Socialite::shouldReceive('driver')->andThrow(new \Exception('oauth failure'));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $response->assertSessionHas('error');
    }

    /**
     * Fake the Socialite google_login driver to return a Google user.
     */
    private function fakeGoogleUser(?string $email): void
    {
        $googleUser = new SocialiteUser;
        $googleUser->id = 'google-id-123';
        $googleUser->name = 'Test User';
        $googleUser->email = $email;
        $googleUser->token = 'fake-access-token';
        $googleUser->refreshToken = 'fake-refresh-token';

        Socialite::fake('google_login', $googleUser);
    }
}
