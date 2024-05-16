<?php

namespace Raorsa\SageMiddlewareClient;


use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Raorsa\RWFileCache\RWFileCache;

class GenericClient
{
    const TOKEN_LIFE_TIME = 518400; // 6 days
    private $url = null;
    private $cache_life = null;
    private $cache_dir = null;
    private $log_dir = null;
    private $lengthCacheData = null;
    private $connexion = null;
    private $email = null;
    private $password = null;
    private $name = null;
    private $cache = null;

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
        $this->log_dir = $log_dir;
        $this->lengthCacheData = $lengthCacheData;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;

        $this->cache = new RWFileCache();

        $this->cache->changeConfig(["cacheDirectory" => $cache_dir, 'gzipCompression' => (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'])]);

        $this->connect();

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

    /**
     * @param string $email
     * @param string $password
     * @param string|null $name
     * @return void
     */
    public function connect(): void
    {
        $token = $this->getCache($this->url);
        if ($token !== '' && $token !== false) {
            $this->log('CACHE LOGIN   ' . $this->url . '||cache->' . substr($token, 0, 10));
            $this->connexion = Connexion::open($this->url, $token, (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG']));
        } else {
            $this->log('SERVER LOGIN  ' . $this->url . '->' . $this->email);
            $this->connexion = Connexion::connect($this->url, $this->email, $this->password, (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG']), $this->name);
        }
    }

    private function saveCache(string $path, string $body, int $lifetime = null)
    {
        if (is_null($lifetime)) {
            $lifetime = $this->cache_life * 60;
        }
        if ($lifetime !== 0) {
            $this->cache->set(md5($path), $body, $lifetime);
        }
    }

    private
    function getCache(string $path): string|bool
    {
        return $this->cache->get(md5($path));
    }

    private
    function removeCache(string $path): bool
    {
        return $this->cache->delete(md5($path));

    }

    private
    function getLast(string $path): string|bool
    {
        return $this->cache->getLast(md5($path));
    }

    private
    function getCacheInfo(string $path): object|bool
    {
        return $this->cache->getObject(md5($path));
    }

    protected
    function call(string $method, bool $cache = true): string|bool
    {
        $token = '';
        $path = $this->url . $method;
        $response = $this->getCache($path);
        $object = $this->getCacheInfo($path);
        $date = 'NO SET';
        if (isset($object->expiryTimestamp)) {
            $date = date("Y-m-d H:i:s", $object->expiryTimestamp);
        }
        $this->log('CACHE RESULT  ' . $path . '||' . $date . '->' . $response);
        if ($response !== false) {
            $this->log('CACHE GET     ' . $path . '||cache->' . $response);
            return $response;
        }

        $response = $this->connexion->call($method, $token);

        if ($response->getStatusCode() === 405) {
            $this->removeCache($this->url);
            $this->connect();
            $response = $this->connexion->call($method, $token);
        }

        if ($token !== '') {
            $this->saveCache($this->url, $token, self::TOKEN_LIFE_TIME);
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
