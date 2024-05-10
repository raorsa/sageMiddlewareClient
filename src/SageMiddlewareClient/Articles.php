<?php

namespace Raorsa\SageMiddlewareClient;

class Articles extends GenericClient
{
    const BASE = 'articles';
    const SCREWTIP = 'screw-tip';

    public function screwTips(bool $cache = true)
    {
        return $this->callJson(implode('/', [self::BASE, self::SCREWTIP, 'list']), $cache);
    }

    public function findScrewTips(string $diameter, bool $cache = true)
    {
        return $this->callJson(implode('/', [self::BASE, self::SCREWTIP, 'find', $diameter]), $cache);
    }

    public function searchScrewTips(array $diameters, bool $cache = true)
    {
        return $this->callJson(implode('/', [self::BASE, self::SCREWTIP, 'search', implode(',', $diameters)]), $cache);
    }

}