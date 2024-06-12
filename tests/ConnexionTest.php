<?php


use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use Raorsa\SageMiddlewareClient\components\connexion;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[CoversClass(connexion::class)]
#[CoversFunction('__construct')]
#[CoversFunction('getInstance')]
#[CoversFunction('getUrl')]
#[CoversFunction('connect')]
#[CoversFunction('open')]
#[CoversFunction('login')]
#[CoversFunction('call')]
class ConnexionTest extends TestCase
{
    private connexion $connexion;

    public function testAutoLogin(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{"token": "12345678"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
            new MockResponse('{"data": "false"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
        ]);

        $this->connexion = $this
            ->getMockBuilder(connexion::class)
            ->setConstructorArgs([$httpClient])
            ->onlyMethods([])
            ->getMock();

        $this->connexion->connect('https://localhost', 'user@domain.com', 'password', false, 'Josevi');
        $token = '';
        $this->connexion->call('test', $token);
        $this->assertNotEmpty($token);
        $this->assertEquals('https://localhost', $this->connexion->getUrl());
    }

    public function testTokenFail(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{"data": "false"}', ['http_code' => 405, 'response_headers' => ['Content-Type: application/json']]),
            new MockResponse('{"token": "12345678"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
            new MockResponse('{"data": "false"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
        ]);

        $this->connexion = $this
            ->getMockBuilder(connexion::class)
            ->setConstructorArgs([$httpClient])
            ->onlyMethods([])
            ->getMock();

        $this->connexion->connect('https://localhost', 'user@domain.com', 'password', false, 'Josevi');
        $this->connexion->open('FALSETOKEN');
        $token = '';
        $this->connexion->call('test', $token);
        $this->assertEquals('12345678', $token);
        $this->assertEquals('https://localhost', $this->connexion->getUrl());
    }

    public function testTokenFound(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse('{"data": "false"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
        ]);

        $this->connexion = $this
            ->getMockBuilder(connexion::class)
            ->setConstructorArgs([$httpClient])
            ->onlyMethods([])
            ->getMock();

        $this->connexion->connect('https://localhost', 'user@domain.com', 'password', false, 'Josevi');
        $this->connexion->open('12345678');
        $token = '';
        $data = $this->connexion->call('test', $token)->getContent();
        $this->assertEquals('{"data": "false"}', $data);
        $this->assertEquals('https://localhost', $this->connexion->getUrl());
    }

}