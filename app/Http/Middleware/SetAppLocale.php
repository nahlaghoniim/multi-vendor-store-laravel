<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SetAppLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->query('locale');

        if (!$locale && $request->route('locale')) {
            $locale = $request->route('locale');
        }

        if (!$locale) {
            $locale = Cookie::get('locale');
        }

        if (!$locale || !array_key_exists($locale, LaravelLocalization::getSupportedLocales())) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        Cookie::queue('locale', $locale, 60 * 24 * 30); // 30 days

        if ($request->route()) {
            \Illuminate\Support\Facades\URL::defaults(['locale' => $locale]);
        }

        return $next($request);
    }
}
