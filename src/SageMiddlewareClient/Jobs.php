<?php

namespace Raorsa\SageMiddlewareClient;

use Symfony\Component\HttpClient\HttpClient;

class Jobs extends GenericClient
{
    const BASE = 'jobs';

    public function list(bool $cache = true)
    {
        return $this->callJson(self::BASE . '/list', $cache);
    }

    public function operations(bool $cache = true)
    {
        return $this->callJson(self::BASE . '/operations', $cache);
    }

    public function operationsJobs(bool $cache = true)
    {
        return $this->callJson(self::BASE . '/operations-jobs', $cache);
    }

    public function info(string $id, bool $cache = true)
    {
        return $this->callJson(self::BASE . '/job/' . $id, $cache);
    }

    public function getSN(string $id, bool $cache = true)
    {
        return $this->callJson(self::BASE . '/jobsn/' . $id, $cache);
    }

    public function importLog(string $date, bool $cache = true)
    {
        return $this->callJson(self::BASE . '/import/' . $date, $cache);
    }
}