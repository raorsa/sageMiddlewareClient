<?php


use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Raorsa\SageMiddlewareClient\components\connexion;
use Raorsa\SageMiddlewareClient\Invoices;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Tests\Traits\baseTest;

#[CoversClass(Invoices::class)]
#[CoversFunction('list')]
#[CoversFunction('info')]
#[CoversFunction('download')]
#[CoversFunction('img')]
class InvoicesTest extends TestCase
{
    use baseTest;

    protected string $base = 'invoices';

    public function testDownload()
    {
        $connexion = $this
            ->getMockBuilder(connexion::class)
            ->setConstructorArgs([new MockHttpClient([
                new MockResponse('{"token": "12345678"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse(file_get_contents(getcwd() . '/tests/resources/' . $this->base . '/' . __FUNCTION__ . '.raw'), ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
            ])])
            ->onlyMethods([])
            ->getMock();
        $connexion->connect('https://localhost/', 'user@domain.com', 'password', false, 'Josevi');

        $element = Invoices::mount($connexion, $this->log, $this->cache);

        $result = $element->download('G-2024-00333', false);
        $this->assertIsString($result);
        $this->assertEquals(file_get_contents(getcwd() . '/tests/resources/' . $this->base . '/' . __FUNCTION__ . '.raw'), $result);
    }

    public function testList()
    {
        $element = Invoices::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->list('0000270', false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testInfo()
    {
        $element = Invoices::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->info('G-2024-00333', false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testImg()
    {
        $connexion = $this
            ->getMockBuilder(connexion::class)
            ->setConstructorArgs([new MockHttpClient([
                new MockResponse('{"token": "12345678"}', ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
                new MockResponse(file_get_contents(getcwd() . '/tests/resources/' . $this->base . '/' . __FUNCTION__ . '.raw'), ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
            ])])
            ->onlyMethods([])
            ->getMock();
        $connexion->connect('https://localhost/', 'user@domain.com', 'password', false, 'Josevi');

        $element = Invoices::mount($connexion, $this->log, $this->cache);

        $result = $element->img('G-2024-00333', false);
        $this->assertIsString($result);
        $this->assertEquals(file_get_contents(getcwd() . '/tests/resources/' . $this->base . '/' . __FUNCTION__ . '.raw'), $result);
    }
}
