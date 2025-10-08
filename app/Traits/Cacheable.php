<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    /**
     * Retrieve data from cache or save it if it doesn't exist yet
     *
     * @param string $key
     * @param int $seconds
     * @param callable $callback
     * @return mixed
     */
    public static function rememberCache(string $key, int $seconds, callable $callback)
    {
        return Cache::remember($key, $seconds, $callback);
    }

    /**
     * Clear cache by key
     *
     * @param string $key
     */
    public static function forgetCache(string $key)
    {
        Cache::forget($key);
    }

    /**
     * Boot method to automatically clear cache when model changes
     */
    protected static function bootCacheable()
    {
        static::saved(function ($model) {
            if (property_exists($model, 'cacheKeys')) {
                foreach ($model->cacheKeys as $key) {
                    Cache::forget($key);
                }
            }
        });

        static::deleted(function ($model) {
            if (property_exists($model, 'cacheKeys')) {
                foreach ($model->cacheKeys as $key) {
                    Cache::forget($key);
                }
            }
        });
    }
}
