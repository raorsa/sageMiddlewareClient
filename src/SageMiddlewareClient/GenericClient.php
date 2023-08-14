<?php

namespace Raorsa\SageMiddlewareClient;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Http;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class GenericClient
{
    const URL_LOGIN = 'login';

    private $url;
    private $login;
    private $cache_life;

    public function __construct(string $url, string $email, string $password, string $name, int $cacheLife = 10)
    {
        $this->url = str_replace('//', '/', $url . '/');
        $this->cache_life = $cacheLife * 60; // in minutes

        $this->login = Http::post($this->url . self::URL_LOGIN, [
            'email' => $email,
            'password' => $password,
            'name' => $name
        ])->json('token');

    }

    private function saveCache(string $path, string $body)
    {
        $directory = (new TemporaryDirectory())
            ->name(md5($path))
            ->create();

        $file = $directory->path(time() + $this->cache_life);
        file_put_contents($file, $body);
    }

    private function getCache(string $path, bool $catchExpired = false): string|bool
    {
        $directory = (new TemporaryDirectory())
            ->name(md5($path))
            ->create();
        $files = scandir($directory);
        sort($files);
        $file = array_shift($files);
        if ($file > time() || $catchExpired) {
            return file_get_contents($directory->path($file));
        } else {
            return false;
        }
    }

    protected function call(string $method, bool $cache = true): PromiseInterface|bool
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
        return $this->call($method, $cache)->json();
    }
}