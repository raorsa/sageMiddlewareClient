<?php

namespace Raorsa\SageMiddlewareClient;

class Invoices extends GenericClient
{
    const BASE = 'invoice';

    public function list(string $team)
    {
        return $this->callJson(self::BASE . '/list/' . $team);
    }

    public function info(string $id)
    {
        return $this->callJson(self::BASE . '/info/' . $id);
    }

    public function download(string $id)
    {
        return $this->call(self::BASE . '/download/' . $id);
    }

    public function img(string $id)
    {
        return $this->call(self::BASE . '/img/' . $id);
    }
}