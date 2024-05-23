<?php

namespace Raorsa\SageMiddlewareClient;

class Jobs extends GenericClient
{
    private const BASE = 'jobs';

    public function list(bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/list', $cache);
    }

    public function operations(bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/operations', $cache);
    }

    public function operationsJobs(bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/operations-jobs', $cache);
    }

    public function info(string $id, bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/job/' . $id, $cache);
    }

    public function getSN(string $id, bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/jobsn/' . $id, $cache);
    }

    public function importLog(string $date, bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/import/' . $date, $cache);
    }
}