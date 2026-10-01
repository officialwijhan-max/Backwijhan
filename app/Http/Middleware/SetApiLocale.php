<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Picks the language of API messages (validation errors, error envelopes).
 *
 * Order: an explicit `locale` field/query value, then the Accept-Language
 * header, then the app default. Only languages we ship translations for are
 * accepted, so the value can never be used to load arbitrary lang files.
 */
class SetApiLocale
{
    public const SUPPORTED = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale($this->resolve($request));

        return $next($request);
    }

    private function resolve(Request $request): string
    {
        $explicit = $request->input('locale', $request->query('locale'));

        if (is_string($explicit) && in_array($explicit, self::SUPPORTED, true)) {
            return $explicit;
        }

        $header = (string) $request->header('Accept-Language', '');
        $primary = strtolower(substr(trim(explode(',', $header)[0]), 0, 2));

        return in_array($primary, self::SUPPORTED, true) ? $primary : config('app.locale');
    }
}
