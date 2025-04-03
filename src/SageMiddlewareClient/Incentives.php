<?php

namespace Raorsa\SageMiddlewareClient;

use Raorsa\SageMiddlewareClient\components\baseClient;

class Incentives extends baseClient
{
    private const BASE = 'incentives';

    public function sat(string $startDate = null, string $endDate = null, bool $cache = true): array|false
    {
        $call = ['sat'];
        if (!is_null($startDate)) {
            $call[] = $startDate;
        }
        if (!is_null($endDate)) {
            $call[] = $endDate;
        }
        return $this->callJson(self::BASE . '/' . implode('/', $call), $cache);
    }


}