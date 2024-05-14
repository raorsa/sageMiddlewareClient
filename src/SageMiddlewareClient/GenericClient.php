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
    private $lengthCacheData;

    public function __construct(string $url, string $email, string $password, string $name = null, int $cacheLife = 10, string $cache_dir = null, string $log_dir = null, int $lengthCacheData = 100)
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
        $this->lengthCacheData = $lengthCacheData;

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
     * @param false|string $data
     * @return void
     */
    public function log($data): void
    {
        if (isset($_ENV['LOG_LEVEL']) && isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] && !is_null($this->log_dir)) {
            $log = new Logger('Sageclient');
            $log->pushHandler(new StreamHandler($this->log_dir . '/raorsa.log', Level::fromName($_ENV['LOG_LEVEL'])));
            if ($this->lengthCacheData !== 0) {
                $data = substr($data, 0, $this->lengthCacheData);
            }
            $log->info($data);
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

    private function getLast(string $path, bool $catchExpired = false): string|bool
    {
        $cache = new RWFileCache();

        $cache->changeConfig(["cacheDirectory" => $this->cache_dir]);

        return $cache->getLast(md5($path));
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
        $date = 'NO SET';
        if (isset($object->expiryTimestamp)) {
            $date = date("Y-m-d H:i:s", $object->expiryTimestamp);
        }
        $this->log('CACHE RESULT  ' . $path . '||' . $date . '->' . $response);
        if ($response != false) {
            $this->log('CACHE GET     ' . $path . '||cache->' . $response);
            return $response;
        }

        if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG']) {
            $response = Http::withoutVerifying()->withToken($this->login)->get($path);
        } else {
            $response = Http::withToken($this->login)->get($path);
        }
        $return = false;
        if ($response->successful()) {
            $return = $response->body();
            if ($cache && $return != '[]') {
                $this->saveCache($path, $response->body());
                $this->log('SERVER RESULT ' . $path . '->' . $return);
            } else {
                $return = false;
            }
        }

        if (!$return) {
            $return = $this->getLast($path);
            $this->log('CACHE LAST    ' . $path . '||cache->' . $response);
        }

        return $return;
    }

    protected function callJson(string $method, bool $cache = true)
    {
        return json_decode($this->call($method, $cache));
    }
}
