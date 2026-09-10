<?php

namespace App\Services\BankTransactions;

use Illuminate\Support\Facades\Str;

/**
 * Structured keyword matching for the automated bank sync. Turns free-form
 * transaction descriptions ("NETFLIX.COM", "GCASH PAY VIA MAYA") into
 * canonical subscription service names.
 */
class SubscriptionKeywordMatcher
{
    /**
     * Known recurring services and the tokens that identify them. Strings are
     * lower-cased before comparison.
     *
     * @var array<string, array<int, string>>
     */
    private const KNOWLEDGE = [
        'Netflix' => ['netflix'],
        'Spotify' => ['spotify'],
        'Disney+' => ['disney', 'disneyplus', 'disney+'],
        'HBO Max' => ['hbo', 'hbo max', 'max'],
        'Prime' => ['amazon', 'prime video'],
        'YouTube' => ['youtube', 'youtube premium'],
        'Apple' => ['apple', 'icloud'],
        'Adobe' => ['adobe', 'creative cloud'],
        'Notion' => ['notion'],
        'GitHub' => ['github'],
        'Microsoft 365' => ['microsoft', 'office 365'],
        'Dropbox' => ['dropbox'],
        'AWS' => ['aws', 'amazon web services'],
        'Vercel' => ['vercel'],
        'Figma' => ['figma'],
        'Linear' => ['linear'],
        'Notion' => ['notion'],
    ];

    /**
     * Map a transaction description to a canonical service name, or null.
     */
    public function match(?string $description): ?string
    {
        if (empty($description)) {
            return null;
        }

        $needle = Str::lower($description);

        foreach (self::KNOWLEDGE as $service => $tokens) {
            foreach ($tokens as $token) {
                if (Str::contains($needle, $token)) {
                    return $service;
                }
            }
        }

        return null;
    }
}
