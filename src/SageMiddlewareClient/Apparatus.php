<?php

namespace Raorsa\SageMiddlewareClient;

class Apparatus extends GenericClient
{
    const BASE = 'apparatus';

    public function list(string $client, bool $cache = true)
    {
        return $this->callJson(self::BASE . '/' . $client);
    }

    public function interventions(string $id, bool $cache = true)
    {
        return $this->callJson(self::BASE . '/' . $id . '/interventions', $cache);
    }

    public function integrated(string $id, bool $cache = true)
    {
        return $this->callJson(self::BASE . '/' . $id . '/integrated', $cache);
    }

    public function brandInfo(string $id, bool $cache = true)
    {
        return $this->callJson(self::BASE . '/brand/' . $id, $cache);
    }

    public function brands(bool $cache = true)
    {
        return $this->callJson(self::BASE . '/brands', $cache);
    }

    public function types(bool $cache = true)
    {
        return $this->callJson(self::BASE . '/types', $cache);
    }

    public function warranties(bool $cache = true)
    {
        return $this->callJson(self::BASE . '/warranties', $cache);
    }

    public function interventionsType(bool $cache = true)
    {
        return $this->callJson(self::BASE . '/interventions-type', $cache);
    }
}