<?php

namespace Raorsa\SageMiddlewareClient;

class Jobs extends GenericClient
{
    const BASE = 'jobs';

    public function list()
    {
        return $this->callJson(self::BASE . '/list');
    }

    public function operations()
    {
        return $this->callJson(self::BASE . '/operations');
    }

    public function operationsJobs()
    {
        return $this->callJson(self::BASE . '/operations-jobs');
    }

    public function info(string $id)
    {
        return $this->callJson(self::BASE . '/job/' . $id);
    }

    public function getSN(string $id)
    {
        return $this->callJson(self::BASE . '/jobsn/' . $id);
    }

    public function importLog(string $date)
    {
        return $this->callJson(self::BASE . '/import/' . $date);
    }
}