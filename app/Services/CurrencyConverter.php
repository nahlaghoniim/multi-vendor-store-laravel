<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class CurrencyConverter
{
    protected string $apiKey;
    protected string $baseUrl = 'https://v6.exchangerate-api.com/v6';

    public function __construct()
    {
        $this->apiKey = config('services.exchange_rate.api_key');

        if (! $this->apiKey) {
            throw new RuntimeException('Exchange Rate API key is missing.');
        }
    }

    public function convert(string $from, string $to, float $amount = 1): float
    {
        $response = Http::get(
            "{$this->baseUrl}/{$this->apiKey}/latest/{$from}"
        );

        if (! $response->successful()) {
            throw new RuntimeException(
                'Currency API request failed: ' . $response->body()
            );
        }

        $rates = $response->json('conversion_rates');

        if (! isset($rates[$to])) {
            throw new RuntimeException("Currency {$to} not supported.");
        }

        return round($rates[$to] * $amount, 2);
    }
}
