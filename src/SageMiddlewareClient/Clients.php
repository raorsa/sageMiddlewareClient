<?php

namespace Raorsa\SageMiddlewareClient;

class Clients extends GenericClient
{
    const BASE = 'clients';

    public function companyInfo(string $domain)
    {
        return $this->callJson(self::BASE . '/team/' . $domain);
    }

    public function clientInfo(string $id)
    {
        return $this->callJson(self::BASE . '/info/' . $id);
    }

    public function validDomains()
    {
        return $this->callJson(self::BASE . '/auth-domains');
    }

    public function clients()
    {
        return $this->callJson(self::BASE . '/list');
    }
}