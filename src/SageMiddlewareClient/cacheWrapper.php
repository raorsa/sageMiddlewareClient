<?php

namespace Raorsa\SageMiddlewareClient;

use Raorsa\RWFileCache\RWFileCache;

class cacheWrapper
{
    private $cache_life = null;
    private $cache = null;

    public function __construct(int $cacheLife = 10, string $cache_dir = null)
    {
        if (is_null($cache_dir)) {
            $cache_dir = "/tmp/sageCache." . md5(getcwd()) . "/";
        }
        $this->cache_life = $cacheLife; // in minutes

        $this->cache = new RWFileCache();

        $compress = (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === "false");

        $this->cache->changeConfig(["cacheDirectory" => $cache_dir, 'gzipCompression' => $compress]);

    }

    public function saveCache(string $path, string $body, int $lifetime = null)
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

    public function removeCache(string $path): bool
    {
        return $this->cache->delete(md5($path));
    }

    public function getLast(string $path): string|bool
    {
        return $this->cache->getLast(md5($path));
    }

    public function getCacheInfo(string $path): object|bool|null
    {
        return $this->cache->getObject(md5($path));
    }

    public function clean()
    {
        $this->cache->flush();
    }
}