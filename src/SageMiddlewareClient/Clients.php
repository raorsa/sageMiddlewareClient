<?php

namespace Raorsa\SageMiddlewareClient;

use Raorsa\SageMiddlewareClient\components\baseClient;

class Clients extends baseClient
{
    private const BASE = 'clients';

    public function companyInfo(string $domain, bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/team/' . $domain, $cache);
    }

    public function clientInfo(string $id, bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/info/' . $id, $cache);
    }

    public function validDomains(bool $cache = true): array|false
    {
        return $this->callJson(self::BASE . '/auth-domains', $cache);
    }

    public function clients(bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/list', $cache);
    }
}