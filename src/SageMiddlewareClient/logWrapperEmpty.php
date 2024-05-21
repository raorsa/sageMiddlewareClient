<?php

namespace Raorsa\SageMiddlewareClient;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class logWrapperEmpty implements LogWrapperInterface
{
    public function logCache($path, $data, $verb)
    {

    }

    public function logServer($path, $data, $verb)
    {

    }
}