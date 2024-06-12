<?php

namespace Tests\Traits;

use Raorsa\SageMiddlewareClient\components\connexion;
use Raorsa\SageMiddlewareClient\wrappers\cache;
use Raorsa\SageMiddlewareClient\wrappers\log;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

trait baseTest
{
    protected log $log;
    protected cache $cache;

    protected function createConnexion(string $function): connexion
    {
        $connexion = $this
            ->getMockBuilder(connexion::class)
            ->setConstructorArgs([new MockHttpClient([
                new MockResponse('{"token": "12345678"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse($this->getData($function), ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
            ])])
            ->onlyMethods([])
            ->getMock();
        $connexion->connect('https://localhost/', 'user@domain.com', 'password', false, 'Josevi');
        return $connexion;
    }

    protected function getData(string $function): string
    {
        return file_get_contents(getcwd() . '/tests/resources/' . $this->base . '/' . $function . '.json');
    }

    protected function setUp(): void
    {
        $this->log = $this->getMockBuilder(log::class)->disableOriginalConstructor()->getMock();
        $this->cache = new cache(1, 'tests/cache/');
    }

    protected function tearDown(): void
    {
        $this->cache->clean();
        unset($this->log, $this->cache);
    }
}