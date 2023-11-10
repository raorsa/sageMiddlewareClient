<?php

namespace Raorsa\SageMiddlewareClient;

use Illuminate\Support\Facades\Http;
use rapidweb\RWFileCache\RWFileCache;

class GenericClient
{
    const URL_LOGIN = 'login';

    private $url;
    private $login;
    private $cache_life;
    private $cache_dir;

    public function __construct(string $url, string $email, string $password, string $name, int $cacheLife = 10, string $cache_dir = null)
    {
        if (is_null($cache_dir)) {
            $cache_dir = "/tmp/sageCache." . md5($_SERVER["SERVER_NAME"]) . "/";
        }
        $this->url = str_replace('//', '/', $url . '/');
        $this->cache_life = $cacheLife; // in minutes
        $this->cache_dir = $cache_dir;

        $this->login = Http::post($this->url . self::URL_LOGIN, [
            'email' => $email,
            'password' => $password,
            'name' => $name
        ])->json('token');

    }

    private function saveCache(string $path, string $body)
    {
        $cache = new RWFileCache();

        $cache->changeConfig(["cacheDirectory" => $this->cache_dir]);

        $cache->set(md5($path), $body, strtotime('+ ' . $this->cache_life . ' minutes'));
    }

    private function getCache(string $path, bool $catchExpired = false): string|bool
    {
        $cache = new RWFileCache();

        $cache->changeConfig(["cacheDirectory" => $this->cache_dir]);

        return $cache->get(md5($path));
    }

    protected function call(string $method, bool $cache = true): string|bool
    {
        $path = $this->url . $method;

        if ($response = $this->getCache($path)) {
            return $response;
        }

        $response = Http::withToken($this->login)->get($path);

        if ($response->successful()) {
            if ($cache) {
                $this->saveCache($path, $response->body());
            }
            $return = $response->body();
        } else {
            if ($cache) {
                $return = $this->getCache($path, true);
            } else {
                $return = false;
            }
        }
        return $return;
    }

    protected function callJson(string $method, bool $cache = true)
    {
        return json_decode($this->call($method, $cache));
    }
}
