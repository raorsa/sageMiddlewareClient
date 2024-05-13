<?php

namespace Raorsa\SageMiddlewareClient;

use Illuminate\Support\Facades\Http;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Raorsa\RWFileCache\RWFileCache;

class GenericClient
{
    const URL_LOGIN = 'login';

    private $url;
    private $login;
    private $cache_life;
    private $cache_dir;
    private $log_dir;

    public function __construct(string $url, string $email, string $password, string $name = null, int $cacheLife = 10, string $cache_dir = null, string $log_dir = null)
    {
        if (is_null($cache_dir)) {
            $cache_dir = "/tmp/sageCache." . md5(getcwd()) . "/";
        }
        if (is_null($log_dir)) {
            $log_dir = (function_exists('storage_path') ? storage_path() . '/' : "") . "logs/";
        }
        $this->url = str_replace('//', '/', $url . '/');
        $this->cache_life = $cacheLife; // in minutes
        $this->cache_dir = $cache_dir;
        $this->log_dir = $log_dir;

        $options = [
            'email' => $email,
            'password' => $password
        ];
        if (!is_null($name)) {
            $options['name'] = $name;
        }

        if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG']) {
            $this->login = Http::withoutVerifying()->post($this->url . self::URL_LOGIN, $options)->json('token');
        } else {
            $this->login = Http::post($this->url . self::URL_LOGIN, $options)->json('token');
        }

    }

    /**
     * @param false|string $return
     * @return void
     */
    public function log($return): void
    {
        if (isset($_ENV['LOG_LEVEL']) && !is_null($this->log_dir)) {
            $log = new Logger('Sageclient');
            $log->pushHandler(new StreamHandler($this->log_dir . '/raorsa.log', Level::fromName($_ENV['LOG_LEVEL'])));
            $log->info($return);
        }
    }

    private function saveCache(string $path, string $body)
    {
        $cache = new RWFileCache();

        $cache->changeConfig(["cacheDirectory" => $this->cache_dir]);

        $cache->set(md5($path), $body, $this->cache_life * 60);
    }

    private function getCache(string $path, bool $catchExpired = false): string|bool
    {
        $cache = new RWFileCache();

        $cache->changeConfig(["cacheDirectory" => $this->cache_dir]);

        return $cache->get(md5($path));
    }

    private function getCacheInfo(string $path, bool $catchExpired = false): object|bool
    {
        $cache = new RWFileCache();

        $cache->changeConfig(["cacheDirectory" => $this->cache_dir]);

        return $cache->getObject(md5($path));
    }

    protected function call(string $method, bool $cache = true): string|bool
    {
        $path = $this->url . $method;
        $response = $this->getCache($path);
        $object = $this->getCacheInfo($path);
        $this->log('CACHE RESULT ' . $path . '||' . date("Y-m-d H:i:s", $object->expiryTimestamp) . '->' . $response);
        if ($response != false) {
            $this->log($path . '||cache->' . $response);
            return $response;
        }

        if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG']) {
            $response = Http::withoutVerifying()->withToken($this->login)->get($path);
        } else {
            $response = Http::withToken($this->login)->get($path);
        }

        if ($response->successful()) {
            $return = $response->body();
            if ($cache && $return != '[]') {
                $this->saveCache($path, $response->body());
            }
        } else {
            $return = false;
        }
        $this->log($path . '->' . $return);
        return $return;
    }

    protected function callJson(string $method, bool $cache = true)
    {
        return json_decode($this->call($method, $cache));
    }
}
