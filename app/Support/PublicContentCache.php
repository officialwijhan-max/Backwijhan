<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Short-lived cache for the public, read-only content endpoints (services,
 * work, pricing). Every key embeds a version number, so `flush()` invalidates
 * the whole set at once on any store (the default `database` store has no
 * cache tags). Models that feed these endpoints call `flush()` whenever an
 * admin saves or deletes a record, so edits show up immediately instead of
 * after the TTL.
 */
class PublicContentCache
{
    /** Seconds a cached response may be served at most, even if no flush happens. */
    public const TTL = 60;

    private const VERSION_KEY = 'public-content.version';

    public static function remember(string $key, Closure $callback): mixed
    {
        return Cache::remember(self::version().".{$key}", self::TTL, $callback);
    }

    public static function flush(): void
    {
        Cache::forever(self::VERSION_KEY, (string) microtime(true));
    }

    private static function version(): string
    {
        return 'public-content.'.Cache::get(self::VERSION_KEY, '0');
    }
}
