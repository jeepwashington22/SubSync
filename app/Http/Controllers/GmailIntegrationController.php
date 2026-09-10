<?php

namespace App\Http\Controllers;

use App\Models\Subcriptions;
use App\Models\User;
use App\Services\GmailReceiptParser;
use Google\Client as GoogleClient;
use Google\Service\Exception as GoogleServiceException;
use Google\Service\Gmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\InvalidStateException;

class GmailIntegrationController extends Controller
{
    /**
     * Gmail search query used to find subscription receipts.
     */
    private const RECEIPT_QUERY = 'subject:(receipt OR "payment successful" OR "subscription renewed" OR invoice) newer_than:1y';

    public function __construct(private GmailReceiptParser $parser) {}

    /**
     * Redirect the user to Google's OAuth consent screen, requesting
     * offline access so we receive a refresh token.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Google sign-in is not configured. Add GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET to your .env file.');
        }

        /** @var GoogleProvider $provider */
        $provider = Socialite::driver('google');

        return $provider
            ->scopes([config('services.google.scopes', 'https://www.googleapis.com/auth/gmail.readonly')])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    /**
     * Handle the Google OAuth callback and persist the tokens.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $e) {
            // The session "state" was lost between redirect and callback,
            // usually because the app was browsed at a different address
            // (e.g. 127.0.0.1 vs localhost) than GOOGLE_REDIRECT_URI.
            // Retry statelessly - the code is still validated with Google.
            try {
                $googleUser = Socialite::driver('google')->stateless()->user();
            } catch (\Throwable $fallback) {
                Log::warning('Google OAuth callback failed', ['error' => $fallback->getMessage()]);

                return redirect()
                    ->route('dashboard')
                    ->with('error', 'Could not connect to Google. Make sure you browse the app at the same address as GOOGLE_REDIRECT_URI (http://localhost:8000), then click Connect Gmail again.');
            }
        } catch (\Throwable $e) {
            Log::warning('Google OAuth callback failed', ['error' => $e->getMessage()]);

            return redirect()
                ->route('dashboard')
                ->with('error', 'Could not connect to Google. Please try again.');
        }

        /** @var User $user */
        $user = $request->user();

        $user->forceFill([
            'google_access_token' => $googleUser->token,
            // Google only returns a refresh token on first consent (or with prompt=consent)
            'google_refresh_token' => $googleUser->refreshToken ?? $user->google_refresh_token,
            'google_token_expires_at' => now()->addSeconds((int) ($googleUser->expiresIn ?? 3600)),
        ])->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Gmail connected successfully! You can now scan your inbox for subscriptions.');
    }

    /**
     * Scan the connected Gmail inbox for subscription receipts and
     * save any new ones as subscriptions.
     */
    public function scanInboxForSubscriptions(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->hasGoogleConnected()) {
            return redirect()->route('dashboard')->with('error', 'Connect Gmail first before scanning.');
        }

        $client = $this->authenticatedGoogleClient($user);

        if ($client === null) {
            return redirect()->route('dashboard')->with('error', 'Gmail connection expired. Please reconnect your account.');
        }

        $gmail = new Gmail($client);

        try {
            $response = $gmail->users_messages->listUsersMessages('me', [
                'q' => self::RECEIPT_QUERY,
                'maxResults' => 50,
            ]);
        } catch (GoogleServiceException $e) {
            Log::error('Gmail scan failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            // 403 insufficientPermissions: the stored token was granted
            // without the Gmail scope - a re-consent is required.
            if (str_contains($e->getMessage(), 'insufficient authentication scopes')
                || str_contains($e->getMessage(), 'insufficientPermissions')) {
                return redirect()->route('dashboard')->with(
                    'error',
                    'Your Gmail connection is missing the required read permission. Disconnect and connect Gmail again, then accept the "Read your email" permission.'
                );
            }

            return redirect()->route('dashboard')->with('error', 'Failed to scan your inbox. Please try again.');
        } catch (\Throwable $e) {
            Log::error('Gmail scan failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return redirect()->route('dashboard')->with('error', 'Failed to scan your inbox. Please try again.');
        }

        $imported = 0;

        foreach ($response->getMessages() ?? [] as $message) {
            $full = $gmail->users_messages->get('me', $message->getId(), ['format' => 'full']);

            $headers = collect($full->getPayload()->getHeaders())
                ->pluck('value', 'name');

            $from = (string) $headers->get('From', '');
            $subject = (string) $headers->get('Subject', '');
            $body = $full->getSnippet() ?? '';

            $parsed = $this->parser->parse($from, $subject, $body);

            if ($parsed === null) {
                continue;
            }

            // Skip subscriptions the user is already tracking
            $exists = Subcriptions::where('user_id', $user->id)
                ->where('name', $parsed['name'])
                ->exists();

            if ($exists) {
                continue;
            }

            Subcriptions::create([
                'user_id' => $user->id,
                'name' => $parsed['name'],
                'price' => $parsed['price'],
                'billing_cycle' => 'monthly',
                'billing_date' => $this->guessNextBillingDate($full->getInternalDate()),
                'category' => 'Imported from Gmail',
            ]);

            $imported++;
        }

        return redirect()
            ->route('dashboard')
            ->with('success', "Scan complete! Imported {$imported} new subscription(s).");
    }

    /**
     * Build a Google_Client with a fresh access token, using the stored
     * refresh token when the access token has expired.
     */
    private function authenticatedGoogleClient(User $user): ?GoogleClient
    {
        $client = new GoogleClient;
        $client->setClientId((string) config('services.google.client_id'));
        $client->setClientSecret((string) config('services.google.client_secret'));
        $client->setScopes([config('services.google.scopes', 'https://www.googleapis.com/auth/gmail.readonly')]);
        $client->setAccessToken($user->google_access_token);

        if ($client->isAccessTokenExpired()) {
            if ($user->google_refresh_token === null) {
                return null;
            }

            try {
                $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            } catch (\Throwable $e) {
                Log::error('Google token refresh failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);

                return null;
            }

            $user->forceFill([
                'google_access_token' => $client->getAccessToken()['access_token'],
                'google_token_expires_at' => now()->addSeconds((int) ($client->getAccessToken()['expires_in'] ?? 3600)),
            ])->save();
        }

        return $client;
    }

    private function guessNextBillingDate(?int $internalDate): Carbon
    {
        // Assume a monthly cycle: next billing is one month after the receipt email date
        $receivedAt = $internalDate !== null
            ? Carbon::createFromTimestamp($internalDate / 1000)
            : now();

        return $receivedAt->addMonth()->startOfDay();
    }

    /**
     * Remove the stored Google tokens so the user can re-consent with
     * the full scope set (e.g. after adding the Gmail scope).
     */
    public function disconnectGoogle(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->forceFill([
            'google_access_token' => null,
            'google_refresh_token' => null,
            'google_token_expires_at' => null,
        ])->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Gmail disconnected. Connect again to grant the required permissions.');
    }
}
