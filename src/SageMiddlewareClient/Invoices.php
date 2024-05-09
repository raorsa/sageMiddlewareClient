<?php

namespace Raorsa\SageMiddlewareClient;

class Invoices extends GenericClient
{
    const BASE = 'invoice';

    public function list(string $team, bool $cache = true)
    {
        return $this->callJson(self::BASE . '/list/' . $team, $cache);
    }

    public function info(string $id, bool $cache = true)
    {
        return $this->callJson(self::BASE . '/info/' . $id, $cache);
    }

    public function download(string $id, bool $cache = true)
    {
        return $this->call(self::BASE . '/download/' . $id, $cache);
    }

    public function img(string $id, bool $cache = true)
    {
        return $this->call(self::BASE . '/img/' . $id, $cache);
    }
}