<?php

namespace App\Services;

/**
 * Extracts a hypothetical service name and price from a Gmail
 * receipt/subscription email snippet or body.
 */
class GmailReceiptParser
{
    /**
     * Well-known subscription sender domains mapped to friendly service names.
     *
     * @var array<string, string>
     */
    private const KNOWN_SERVICES = [
        'netflix.com' => 'Netflix',
        'spotify.com' => 'Spotify',
        'disneyplus.com' => 'Disney+',
        'hulu.com' => 'Hulu',
        'adobe.com' => 'Adobe',
        'apple.com' => 'Apple',
        'amazon.com' => 'Amazon Prime',
        'youtube.com' => 'YouTube Premium',
        'dropbox.com' => 'Dropbox',
        'notion.so' => 'Notion',
        'github.com' => 'GitHub',
        'microsoft.com' => 'Microsoft 365',
    ];

    /**
     * Parse a receipt email into a service name and price.
     *
     * @param  string  $from  The "From" header value (e.g. "Netflix <info@netflix.com>")
     * @param  string  $body  Snippet or decoded body text
     * @return array{name: string, price: float, currency: string}|null
     */
    public function parse(string $from, string $subject, string $body): ?array
    {
        $serviceName = $this->detectServiceName($from, $subject);

        if ($serviceName === null) {
            return null;
        }

        $amount = $this->detectPrice($body);

        if ($amount === null) {
            return null;
        }

        return [
            'name' => $serviceName,
            'price' => $amount['price'],
            'currency' => $amount['currency'],
        ];
    }

    /**
     * @return array{price: float, currency: string}|null
     */
    private function detectPrice(string $text): ?array
    {
        // Matches common currency formats: $12.99, USD 12.99, 12,99 €, £9.99
        if (preg_match('/(?:USD|EUR|GBP|\$|€|£)\s?([\d,]+(?:\.\d{2})?)|([\d,]+(?:\.\d{2})?)\s?(?:USD|EUR|GBP|\$|€|£)/i', $text, $matches) === 1) {
            $raw = $matches[1] !== '' ? $matches[1] : ($matches[2] ?? '');
            $raw = str_replace(',', '', $raw);
            $symbol = strtolower(trim(str_replace([$matches[1], $matches[2] ?? ''], '', $matches[0])));

            $currency = match (true) {
                str_contains($symbol, 'eur') || str_contains($symbol, '€') => 'EUR',
                str_contains($symbol, 'gbp') || str_contains($symbol, '£') => 'GBP',
                default => 'USD',
            };

            return ['price' => (float) $raw, 'currency' => $currency];
        }

        return null;
    }

    private function detectServiceName(string $from, string $subject): ?string
    {
        // 1. Known sender domain
        if (preg_match('/@([a-z0-9.\-]+)/i', $from, $matches) === 1) {
            $domain = strtolower($matches[1]);

            foreach (self::KNOWN_SERVICES as $knownDomain => $name) {
                if (str_ends_with($domain, $knownDomain)) {
                    return $name;
                }
            }
        }

        // 2. Fall back to the display name in the From header, e.g. "Spotify <no-reply@spotify.com>"
        if (preg_match('/^"?([^"<]+?)"?\s*</', $from, $matches) === 1) {
            return trim($matches[1]) !== '' ? trim($matches[1]) : null;
        }

        // 3. Fall back to the first word(s) of the subject before a receipt keyword
        if (preg_match('/^(.+?)(?:\s+(?:receipt|payment|invoice|subscription))/i', $subject, $matches) === 1) {
            return trim($matches[1]);
        }

        return null;
    }
}
