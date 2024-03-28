<?php

namespace App\Traits;

use App\Models\Company;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache as FacadesCache;

trait Cache
{
    protected $identifier;

    protected $cacheDuration = 3600;

    public function setCacheIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /*
     * @param Company $key | String $key
     * @param Closure $closure | String $key
     * @param Request $unique | String $unique
     */
    public function remember($key, Closure $closure, $unique = null)
    {
        throw_unless($this->identifier, new Exception('Must define identifier.'));
        if ($key instanceof Company) {
            $key = $key->slug;
        }
        $cacheKey = $this->makeCacheKey($key, $unique);

        return FacadesCache::remember($cacheKey, $this->cacheDuration, function () use ($closure) {
            return $closure();
        });
    }

    public function forget($key, $unique = null): void
    {
        if (! $this->identifier) {
            return;
        }
        if ($key instanceof Company) {
            $key = $key->slug;
        }
        $cacheKey = $this->makeCacheKey($key);
        FacadesCache::forget($cacheKey);
        if ($unique) {
            $idCacheKey = $this->makeCacheKey($key, $unique);
            FacadesCache::forget($idCacheKey);
        }
    }

    private function makeCacheKey(string $key, $unique = null): string
    {
        if ($unique) {
            $unique = $unique instanceof Request ? $unique->all() : $unique;
            $unique = '-'.md5(serialize($unique));
        }

        return "{$key}:{$this->identifier}{$unique}";
    }
}
