<?php

namespace Raorsa\SageMiddlewareClient;

interface LogWrapperInterface
{
    public function logCache($path, $data, $verb);

    public function logServer($path, $data, $verb);
}