<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Raorsa\SageMiddlewareClient\components\baseClient;
use Raorsa\SageMiddlewareClient\components\connexion;
use Raorsa\SageMiddlewareClient\CustomClient;
use Raorsa\SageMiddlewareClient\wrappers\cache;
use Raorsa\SageMiddlewareClient\wrappers\log;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[CoversClass(baseClient::class)]
#[CoversClass(CustomClient::class)]
class ClientTest extends TestCase
{
    private log $log;
    private cache $cache;

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

    public function testCallWithOutCache(): void
    {
        $connexion = $this
            ->getMockBuilder(connexion::class)
            ->setConstructorArgs([new MockHttpClient([
                new MockResponse('{"token": "12345678"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse('{"data": "true"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
            ])])
            ->onlyMethods([])
            ->getMock();
        $connexion->connect('https://localhost/', 'user@domain.com', 'password', false, 'Josevi');

        $client = CustomClient::mount($connexion, $this->log, $this->cache);

        $result = $client->getJson(__FUNCTION__, false);
        $this->assertEquals("true", $result->data);
    }

    public function testCallWithCache(): void
    {
        $connexion = $this
            ->getMockBuilder(connexion::class)
            ->setConstructorArgs([new MockHttpClient([
                new MockResponse('{"token": "12345678"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse('{"data": "true"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse('{"data": "false"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
            ])])
            ->onlyMethods([])
            ->getMock();
        $connexion->connect('https://localhost/', 'user@domain.com', 'password', false, 'Josevi');

        $client = CustomClient::mount($connexion, $this->log, $this->cache);

        $client->getJson(__FUNCTION__);
        $result = $client->getJson(__FUNCTION__);
        $this->assertEquals("true", $result->data);
    }

    public function testCallNoServer(): void
    {
        $connexion = $this
            ->getMockBuilder(connexion::class)
            ->setConstructorArgs([new MockHttpClient([
                new MockResponse('', ['http_code' => 500, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse('', ['http_code' => 500, 'response_headers' => ['Content-Type: application/json']]),
            ])])
            ->onlyMethods([])
            ->getMock();
        $connexion->connect('https://localhost/', 'user@domain.com', 'password', false, 'Josevi');

        $client = CustomClient::mount($connexion, $this->log, $this->cache);

        $result = $client->getJson(__FUNCTION__);
        $this->assertFalse($result);
    }

    public function testCallCacheLast(): void
    {
        $this->cache->saveCache('https://localhost/' . __FUNCTION__, '{"data": "true"}', time() + 1);
        sleep(3);

        $connexion = $this
            ->getMockBuilder(connexion::class)
            ->setConstructorArgs([new MockHttpClient([
                new MockResponse('{"token": "12345678"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse('{"data": "false"}', ['http_code' => 500, 'response_headers' => ['Content-Type: application/json']]),
            ])])
            ->onlyMethods([])
            ->getMock();

        $connexion->connect('https://localhost/', 'user@domain.com', 'password', false, 'Josevi');

        $client = CustomClient::mount($connexion, $this->log, $this->cache);
        $result = $client->getJson(__FUNCTION__);
        $this->assertEquals("true", $result->data);
    }
}
