<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleLoginController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen for login.
     */
    public function redirect(): RedirectResponse
    {
        if (empty(config('services.google_login.client_id')) || empty(config('services.google_login.client_secret'))) {
            return redirect()
                ->route('login')
                ->with('error', 'Google sign-in is not configured. Add GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET to your .env file.');
        }

        return Socialite::driver('google_login')->redirect();
    }

    /**
     * Handle the Google OAuth callback: find or create the user and log them in.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $provider */
            $provider = Socialite::driver('google_login');
            $googleUser = $provider->user();
        } catch (InvalidStateException $e) {
            // The session "state" was lost between redirect and callback,
            // usually because the app was browsed at a different address
            // (e.g. 127.0.0.1 vs localhost) than the configured redirect URI.
            // Retry statelessly - the code is still validated with Google.
            try {
                /** @var \Laravel\Socialite\Two\AbstractProvider $provider */
                $provider = Socialite::driver('google_login');
                $googleUser = $provider->stateless()->user();
            } catch (\Throwable $fallback) {
                Log::warning('Google login callback failed', ['error' => $fallback->getMessage()]);

                return redirect()
                    ->route('login')
                    ->with('error', 'Could not sign in with Google. Make sure you browse the app at the same address as GOOGLE_LOGIN_REDIRECT_URI (http://localhost:8000), then try again.');
            }
        } catch (\Throwable $e) {
            Log::warning('Google login callback failed', ['error' => $e->getMessage()]);

            return redirect()
                ->route('login')
                ->with('error', 'Could not sign in with Google. Please try again.');
        }

        if ($googleUser->email === null) {
            return redirect()
                ->route('login')
                ->with('error', 'Your Google account did not share an email address, which is required to sign in.');
        }

        /** @var User $user */
        $user = User::query()->firstOrNew(['email' => $googleUser->email]);

        if (! $user->exists) {
            $user->forceFill([
                'name' => $googleUser->name ?? $googleUser->email,
                // Google accounts have no password; password login stays disabled.
                'password' => null,
                'email_verified_at' => now(),
            ])->save();
        } elseif ($user->email_verified_at === null) {
            // Google has verified this email address.
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        // The user just signed in with Google, so mark the account as a
        // Google-provider account regardless of how it was originally created.
        // This keeps the Gmail auto-connect/scan flow and the dashboard UI
        // consistent for existing users who switch to Google sign-in.
        if ($user->auth_provider !== 'google') {
            $user->forceFill(['auth_provider' => 'google'])->save();
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        // The user signed in with their Google/Gmail account, so their Gmail
        // is already tied to this login. Chain straight into the Gmail OAuth
        // consent flow (one-time, to obtain the gmail.readonly scope and a
        // refresh token). After consent, the inbox is scanned automatically.
        if ($user->auth_provider === 'google' && ! $user->hasGoogleConnected()) {
            return redirect()->route('gmail.redirect');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
