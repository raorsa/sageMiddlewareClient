<?php

namespace Raorsa\SageMiddlewareClient;

use Raorsa\SageMiddlewareClient\components\baseClient;

class Invoices extends baseClient
{
    private const BASE = 'invoice';

    public function list(string $team, bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/list/' . $team, $cache);
    }

    public function info(string $id, bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/info/' . $id, $cache);
    }

    public function download(string $id, bool $cache = true): string|false
    {
        return $this->call(self::BASE . '/download/' . $id, $cache);
    }

    public function img(string $id, bool $cache = true): string|false
    {
        return $this->call(self::BASE . '/img/' . $id, $cache);
    }
}