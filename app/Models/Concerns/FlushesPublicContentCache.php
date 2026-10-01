<?php

namespace App\Models\Concerns;

use App\Support\PublicContentCache;

/**
 * Add to every model whose rows are exposed through the cached public
 * endpoints, so an admin edit invalidates the cache straight away.
 */
trait FlushesPublicContentCache
{
    public static function bootFlushesPublicContentCache(): void
    {
        static::saved(fn () => PublicContentCache::flush());
        static::deleted(fn () => PublicContentCache::flush());
    }
}
