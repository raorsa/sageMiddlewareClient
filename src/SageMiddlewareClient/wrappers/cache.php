<?php

namespace Raorsa\SageMiddlewareClient\wrappers;

use Raorsa\RWFileCache\RWFileCache;

class cache
{
    private int $cache_life;
    private RWFileCache $cache;

    public function __construct(int $cacheLife = 10, string $cache_dir = null, bool $compress = false)
    {
        if (is_null($cache_dir)) {
            $cache_dir = "/tmp/sageCache." . md5(getcwd()) . "/";
        }
        $this->cache_life = $cacheLife; // in minutes

        $this->cache = new RWFileCache();

        $this->cache->changeConfig(["cacheDirectory" => $cache_dir, 'gzipCompression' => $compress]);

    }

    public function saveCache(string $path, string $body, int $lifetime = null): void
    {
        if (is_null($lifetime)) {
            $lifetime = $this->cache_life * 60;
        }
        if ($lifetime !== 0) {
            $this->cache->set(md5($path), $body, $lifetime);
        }
    }

    public function getCache(string $path): string|bool
    {
        return $this->cache->get(md5($path));
    }

    public function getLast(string $path): string|bool
    {
        return $this->cache->getLast(md5($path));
    }

    public function clean(): void
    {
        $this->cache->flush();
    }
}