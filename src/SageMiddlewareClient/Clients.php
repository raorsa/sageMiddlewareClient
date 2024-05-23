<?php

namespace Raorsa\SageMiddlewareClient;

class Clients extends GenericClient
{
    private const BASE = 'clients';

    public function companyInfo(string $domain, bool $cache = true): object
    {
        return $this->callJson(self::BASE . '/team/' . $domain, $cache);
    }

    public function clientInfo(string $id, bool $cache = true): object
    {
        return $this->callJson(self::BASE . '/info/' . $id, $cache);
    }

    public function validDomains(bool $cache = true): object
    {
        return $this->callJson(self::BASE . '/auth-domains', $cache);
    }

    public function clients(bool $cache = true): object
    {
        return $this->callJson(self::BASE . '/list', $cache);
    }
}