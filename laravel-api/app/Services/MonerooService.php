<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;

class MonerooService
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected ?string $webhookSecret;

    public function __construct()
    {
        $this->baseUrl       = SiteSetting::get('moneroo_base_url')
                               ?? config('services.moneroo.base_url', 'https://api.moneroo.io/v1');
        $this->apiKey        = SiteSetting::get('moneroo_api_key')
                               ?? config('services.moneroo.api_key');
        $this->webhookSecret = SiteSetting::get('moneroo_webhook_secret')
                               ?? config('services.moneroo.webhook_secret');
    }

    public function createPaymentSession(array $metadata, float $amount, string $currency = 'XOF'): array
    {
        $payload = [
            'amount'      => (int) round($amount),
            'currency'    => strtoupper($currency),
            'description' => 'Commande Tech Pro Futur',
            'return_url'  => route('shop.checkout.success'),
            'cancel_url'  => route('shop.checkout.cancel'),
            'customer'    => [
                'email'      => $metadata['customer_email'] ?? '',
                'first_name' => explode(' ', $metadata['customer_name'] ?? 'Client')[0],
                'last_name'  => implode(' ', array_slice(explode(' ', $metadata['customer_name'] ?? 'Client'), 1)) ?: '-',
            ],
            'metadata'    => collect($metadata)->map(fn($v) => is_array($v) || is_object($v) ? json_encode($v) : $v)->all(),
        ];

        $response = Http::withToken($this->apiKey)
            ->acceptJson()
            ->timeout(15)
            ->post("{$this->baseUrl}/payments/initialize", $payload);

        if (! $response->successful()) {
            throw new \RuntimeException('Moneroo payment session failed: ' . $response->body());
        }

        return $response->json();
    }

    public function webhookSecretConfigured(): bool
    {
        return ! empty($this->webhookSecret);
    }

    public function validateWebhookSignature(string $payload, ?string $signature): bool
    {
        if (empty($this->webhookSecret) || empty($signature)) {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $this->webhookSecret);

        return hash_equals($expected, $signature);
    }
}
