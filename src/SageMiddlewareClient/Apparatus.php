<?php

namespace Raorsa\SageMiddlewareClient;

use Raorsa\SageMiddlewareClient\components\baseClient;

class Apparatus extends baseClient
{
    private const BASE = 'apparatus';

    public function list(string $client, bool $cache = true): array|false
    {
        return $this->callJson(self::BASE . '/' . $client, $cache);
    }

    public function interventions(string $id, bool $cache = true): array|false
    {
        return $this->callJson(self::BASE . '/' . $id . '/interventions', $cache);
    }

    public function integrated(string $id, bool $cache = true): array|false
    {
        return $this->callJson(self::BASE . '/' . $id . '/integrated', $cache);
    }

    public function brandInfo(string $id, bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/brand/' . $id, $cache);
    }

    public function brands(bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/brands', $cache);
    }

    public function types(bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/types', $cache);
    }

    public function warranties(bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/warranties', $cache);
    }

    public function interventionsType(bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/interventions-type', $cache);
    }
}