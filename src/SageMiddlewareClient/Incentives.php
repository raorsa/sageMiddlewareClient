<?php

namespace Raorsa\SageMiddlewareClient;

use Raorsa\SageMiddlewareClient\components\baseClient;

class Incentives extends baseClient
{
    private const BASE = 'incentives';

    public function sat(string $startDate = null, string $endDate = null, bool $cache = true): object|false
    {
        $call = ['sat', 'all'];
        if (!is_null($startDate)) {
            $call[] = $startDate;
        }
        if (!is_null($endDate)) {
            $call[] = $endDate;
        }
        return $this->callJson(self::BASE . '/' . implode('/', $call), $cache);
    }


    public function userSAT(string $user, bool $cache = true): object|false
    {
        return $this->callJson(self::BASE . '/sat/user/' . $user, $cache);
    }

    public function satDetails(string $user, string $startDate = null, string $endDate = null, bool $cache = true): object|false
    {
        $call = ['sat', 'details', $user];
        if (!is_null($startDate)) {
            $call[] = $startDate;
        }
        if (!is_null($endDate)) {
            $call[] = $endDate;
        }
        return $this->callJson(self::BASE . '/' . implode('/', $call), $cache);
    }
}