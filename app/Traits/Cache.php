<?php

namespace App\Traits;

use App\Models\Company;
use Closure;
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

    public function remember(Company $company, Closure $closure, $unique = null)
    {
        if (!$this->identifier) {
            return $closure();
        }
        $cacheKey = $this->makeCacheKey($company->slug, $unique);
        return FacadesCache::remember($cacheKey, $this->cacheDuration, function () use ($closure) {
            return $closure();
        });
    }

    public function forget(Company $company, $unique = null): void
    {
        if (!$this->identifier) {
            return;
        }
        $cacheKey = $this->makeCacheKey($company->slug);
        FacadesCache::forget($cacheKey);
        if ($unique) {
            $idCacheKey = $this->makeCacheKey($company->slug, $unique);
            FacadesCache::forget($idCacheKey);
        }
    }

    private function makeCacheKey(string $slug, $unique = null): string
    {
        if ($unique) {
            $unique = $unique instanceof Request ? $unique->all() : $unique;
            $unique = "-" . md5(serialize($unique));
        }
        return "company-{$slug}:{$this->identifier}{$unique}";
    }
}
