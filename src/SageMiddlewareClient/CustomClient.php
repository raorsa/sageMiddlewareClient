<?php

namespace Raorsa\SageMiddlewareClient;

use Raorsa\SageMiddlewareClient\components\baseClient;

class CustomClient extends baseClient
{
    public function get(string $path, bool $cache = true): string|false
    {
        return $this->call($path, $cache);
    }

    public function getJson(string $path, bool $cache = true): string|array|object|false
    {
        return $this->callJson($path, $cache);
    }

}