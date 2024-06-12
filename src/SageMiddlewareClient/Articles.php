<?php

namespace Raorsa\SageMiddlewareClient;

use Raorsa\SageMiddlewareClient\components\baseClient;

class Articles extends baseClient
{
    private const BASE = 'articles';
    private const SCREWTIP = 'screw-tip';

    public function screwTips(bool $cache = true): array|false
    {
        return $this->callJson(implode('/', [self::BASE, self::SCREWTIP, 'list']), $cache);
    }

    public function findScrewTips(string $diameter, bool $cache = true): array|false
    {
        return $this->callJson(implode('/', [self::BASE, self::SCREWTIP, 'find', $diameter]), $cache);
    }

    public function searchScrewTips(array $diameters, bool $cache = true): array|false
    {
        return $this->callJson(implode('/', [self::BASE, self::SCREWTIP, 'search', implode(',', $diameters)]), $cache);
    }

}