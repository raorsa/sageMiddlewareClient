<?php

namespace Raorsa;

class Jobs extends SageMiddlewareClient
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
}