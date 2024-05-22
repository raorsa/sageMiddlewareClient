<?php

namespace Raorsa\SageMiddlewareClient;

use Symfony\Component\HttpClient\HttpClient;

class GenericClient
{
    const TOKEN_LIFE_TIME = 518400; // 6 days
    private $cache = null;
    private $log = null;
    private $connexion = null;

    public function __construct(string $url, string $user, string $password, int $cacheLife = 10, string $cache_dir = null, bool $verify = true, string $name = 'SageClient')
    {
        $connection = Connexion::getInstance(HttpClient::create());
        $connection->connect($url, $user, $password, $verify, $name);
        $this->connexion = $connection;
        $this->log = new logWrapper();
        $this->cache = new cacheWrapper($cacheLife, $cache_dir);
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
        $response = $this->connexion->call($method, $token);

        if ($token !== '' && !is_null($token) && $useCache) {
            $this->cache->saveCache($this->connexion->getUrl(), $token, self::TOKEN_LIFE_TIME);
            if ($oldToken !== $token) {
                $this->log->logServer($this->connexion->getUrl(), substr($token, 0, 10), 'LOGIN');
            }
        }

        $return = false;
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $return = $response->getContent();
            if ($useCache && $return !== '') {
                $this->cache->saveCache($path, $return);
                $this->log->logServer($path, $return, 'RESULT');
            }
        }


        if ($useCache && $return !== '') {
            $return = $this->cache->getLast($path);
            $this->log->logCache($path, $response, 'LAST');
        }

        return $return;
    }

    protected function callJson(string $method, bool $cache = true)
    {
        return json_decode($this->call($method, $cache));
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
