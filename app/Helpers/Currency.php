<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use NumberFormatter;

class Currency
{
    public static function format($amount, $currency = null)
    {
        $baseCurrency = config('app.currency', 'USD');

        $currency = $currency ?? Session::get('currency_code', $baseCurrency);

        $cacheKey = "currency_rate_{$baseCurrency}_{$currency}";

        $rate = Cache::get($cacheKey, 1);

        $convertedAmount = $amount * $rate;

        $formatter = new NumberFormatter(config('app.locale', 'en_US'), NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($convertedAmount, $currency);
    }
}
