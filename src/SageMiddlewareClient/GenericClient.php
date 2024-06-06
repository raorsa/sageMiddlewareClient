<?php

namespace Raorsa\SageMiddlewareClient;

use Throwable;

class GenericClient
{
    private const TOKEN_LIFE_TIME = 518400; // 6 days
    private cacheWrapper $cache;
    private logWrapperInterface $log;
    private Connexion $connexion;

    protected function __construct(Connexion $connection, logWrapperInterface $logWrapper, cacheWrapper $cache)
    {
        $this->connexion = $connection;
        $this->log = $logWrapper;
        $this->cache = $cache;
    }

    public static function make(string $url, string $user, string $password, bool $verify = true, string $name = 'SageClient', int $cacheLife = 10, string $cacheDir = null, bool $cacheCompress = true, string $logDir = null, int $logLengthData = 100): GenericClient
    {
        return new static(Connexion::mount($url, $user, $password, $verify, $name), new logWrapper($logDir, $logLengthData), new cacheWrapper($cacheLife, $cacheDir, $cacheCompress));
    }

    public static function mount(Connexion $connection, logWrapperInterface $logWrapper, cacheWrapper $cache): GenericClient
    {
        return new static($connection, $logWrapper, $cache);
    }

    protected function call(string $method, bool $useCache = true): string|bool
    {
        $path = $this->connexion->getUrl() . $method;

        if ($useCache && ($response = $this->cache->getCache($path))) {
            $this->log->logCache($path, $response, 'GET');
            return $response;
        }
        $token = '';
        if ($useCache) {
            $token = $this->cache->getCache($this->connexion->getUrl());
            if ($token !== '' && $token !== false) {
                $this->log->logCache($this->connexion->getUrl(), substr($token, 0, 10), 'LOGIN');
                $this->connexion->open($token);
            }
        }

        $oldToken = $token;

        try {
            $response = $this->connexion->call($method, $token);
            $code = $response->getStatusCode();
            $return = $response->getContent();
        } catch (Throwable) {
            $code = 500;
            $return = false;
            $response = null;
        }
        if ($token !== '' && !is_null($token) && $useCache) {
            $this->cache->saveCache($this->connexion->getUrl(), $token, self::TOKEN_LIFE_TIME);
            if ($oldToken !== $token) {
                $this->log->logServer($this->connexion->getUrl(), substr($token, 0, 10), 'LOGIN');
            }
        }


        if ($code >= 200 && $code < 300 && $useCache && $return !== '' && $return !== false) {
            $this->cache->saveCache($path, $return);
            $this->log->logServer($path, $return, 'RESULT');
        }


        if ($useCache && $return !== '') {
            $return = $this->cache->getLast($path);
            $this->log->logCache($path, $return, 'LAST');
        }

        return $return;
    }

    protected function callJson(string $method, bool $cache = true): object|false|array|string
    {
        try {
            return json_decode($this->call($method, $cache), false, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            return false;
        }

    }

    public function setCache(?cacheWrapper $cache): void
    {
        $this->cache = $cache;
    }

    public function setLog(?LogWrapperInterface $log): void
    {
        $this->log = $log;
    }

    public function setConnexion(Connexion $connexion): void
    {
        $this->connexion = $connexion;
    }

    public function setVerify(bool $verify): void
    {
        $this->connexion->setVerify($verify);
    }
}
