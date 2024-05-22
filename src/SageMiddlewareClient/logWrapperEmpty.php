<?php

namespace Raorsa\SageMiddlewareClient;
class logWrapperEmpty implements LogWrapperInterface
{
    public function logCache(string $path, string $data, string $verb): void
    {

    }

    public function logServer(string $path, string $data, string $verb): void
    {

    }
}