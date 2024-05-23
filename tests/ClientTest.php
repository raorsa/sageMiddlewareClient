<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Raorsa\SageMiddlewareClient\Apparatus;
use Raorsa\SageMiddlewareClient\Articles;
use Raorsa\SageMiddlewareClient\cacheWrapper;
use Raorsa\SageMiddlewareClient\Clients;
use Raorsa\SageMiddlewareClient\Connexion;
use Raorsa\SageMiddlewareClient\DeliveryNotes;
use Raorsa\SageMiddlewareClient\GenericClient;
use Raorsa\SageMiddlewareClient\Invoices;
use Raorsa\SageMiddlewareClient\Jobs;
use Raorsa\SageMiddlewareClient\logWrapperEmpty;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[CoversClass(GenericClient::class)]
#[CoversClass(Apparatus::class)]
#[CoversClass(Articles::class)]
#[CoversClass(Clients::class)]
#[CoversClass(DeliveryNotes::class)]
#[CoversClass(Invoices::class)]
#[CoversClass(Jobs::class)]
class ClientTest extends TestCase
{
    private logWrapperEmpty $log;
    private cacheWrapper $cache;

    protected function setUp(): void
    {
        $this->log = new logWrapperEmpty();
        $this->cache = new cacheWrapper(1, 'tests/cache/');
    }

    protected function tearDown(): void
    {
        $this->cache->clean();
        unset($this->log, $this->cache);
    }

    public function testCallWithOutCache(): void
    {
        $connexion = $this
            ->getMockBuilder(Connexion::class)
            ->setConstructorArgs([new MockHttpClient([
                new MockResponse('{"token": "12345678"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse('{"data": "true"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
            ])])
            ->onlyMethods([])
            ->getMock();
        $connexion->connect('https://localhost/', 'user@domain.com', 'password', false, 'Josevi');

        $job = Jobs::mount($connexion, $this->log, $this->cache);

        $result = $job->list(false);
        $this->assertEquals("true", $result->data);
    }

    public function testCallWithCache(): void
    {
        $connexion = $this
            ->getMockBuilder(Connexion::class)
            ->setConstructorArgs([new MockHttpClient([
                new MockResponse('{"token": "12345678"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse('{"data": "true"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse('{"data": "false"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
            ])])
            ->onlyMethods([])
            ->getMock();
        $connexion->connect('https://localhost/', 'user@domain.com', 'password', false, 'Josevi');

        $job = Jobs::mount($connexion, $this->log, $this->cache);

        $job->list();
        $result = $job->list();
        $this->assertEquals("true", $result->data);
    }

    public function testCallNoServer(): void
    {
        $connexion = $this
            ->getMockBuilder(Connexion::class)
            ->setConstructorArgs([new MockHttpClient([
                new MockResponse('', ['http_code' => 500, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse('', ['http_code' => 500, 'response_headers' => ['Content-Type: application/json']]),
            ])])
            ->onlyMethods([])
            ->getMock();
        $connexion->connect('https://localhost/', 'user@domain.com', 'password', false, 'Josevi');

        $job = Jobs::mount($connexion, $this->log, $this->cache);

        $result = $job->list();
        $this->assertFalse($result);
    }

    public function testCallCacheLast(): void
    {
        $this->cache->saveCache('https://localhost/jobs/list', '{"data": "true"}', time() + 1);
        sleep(3);

        $connexion = $this
            ->getMockBuilder(Connexion::class)
            ->setConstructorArgs([new MockHttpClient([
                new MockResponse('{"token": "12345678"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse('{"data": "false"}', ['http_code' => 500, 'response_headers' => ['Content-Type: application/json']]),
            ])])
            ->onlyMethods([])
            ->getMock();

        $connexion->connect('https://localhost/', 'user@domain.com', 'password', false, 'Josevi');

        $job = Jobs::mount($connexion, $this->log, $this->cache);
        $result = $job->list();
        $this->assertEquals("true", $result->data);
    }
}
