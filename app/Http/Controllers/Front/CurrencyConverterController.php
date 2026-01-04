<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CurrencyConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class CurrencyConverterController extends Controller
{
    public function store(Request $request, CurrencyConverter $converter)
    {
        $request->validate([
            'currency_code' => 'required|string|size:3',
        ]);

        $baseCurrencyCode = config('app.currency'); 
        $currencyCode = strtoupper($request->currency_code);

        $cacheKey = "currency_rate_{$baseCurrencyCode}_{$currencyCode}";

        $rate = Cache::remember(
            $cacheKey,
            now()->addHours(12), 
            fn () => $converter->convert($baseCurrencyCode, $currencyCode)
        );

        Session::put('currency_code', $currencyCode);
        Session::put('currency_rate', $rate);

        return redirect()->back();
    }
}
