<?php

namespace Raorsa\SageMiddlewareClient;
class logWrapperEmpty implements LogWrapperInterface
{
    public function logCache($path, $data, $verb)
    {

    }

    public function logServer($path, $data, $verb)
    {

    }
}