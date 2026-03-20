<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if (! $locale) {
            $defaultLanguage = Language::where('is_default', true)->first();
            $locale = $defaultLanguage ? $defaultLanguage->code : config('app.locale');
        }

        if ($locale) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
