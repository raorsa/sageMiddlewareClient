<?php

namespace Raorsa\SageMiddlewareClient;

class Articles extends GenericClient
{
    const BASE = 'articles';
    const SCREWTIP = 'screw-tip';

    public function screwTips()
    {
        return $this->callJson(implode('/', [self::BASE, self::SCREWTIP, 'list']));
    }

    public function findScrewTips(string $diameter)
    {
        return $this->callJson(implode('/', [self::BASE, self::SCREWTIP, 'find', $diameter]));
    }

    public function searchScrewTips(array $diameters)
    {
        return $this->callJson(implode('/', [self::BASE, self::SCREWTIP, 'search', implode(',', $diameters)]));
    }

}