<?php

namespace Raorsa;

class Clients extends SageMiddlewareClient
{
    const BASE = 'clients';

    public function companyInfo(string $domain)
    {
        return $this->callJson(self::BASE . '/team-info/' . $domain);
    }

    public function validDomains()
    {
        return $this->callJson(self::BASE . '/auth-domains');
    }
}