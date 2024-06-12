<?php


use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Raorsa\SageMiddlewareClient\components\connexion;
use Raorsa\SageMiddlewareClient\DeliveryNotes;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Tests\Traits\baseTest;

#[CoversClass(DeliveryNotes::class)]
#[CoversFunction('list')]
#[CoversFunction('info')]
#[CoversFunction('download')]
#[CoversFunction('img')]
class DeliveryNotesTest extends TestCase
{
    use baseTest;

    protected string $base = 'deliveryNotes';

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

        $element = DeliveryNotes::mount($connexion, $this->log, $this->cache);

        $result = $element->download('CI-2024-0000062', false);
        $this->assertIsString($result);
        $this->assertEquals(file_get_contents(getcwd() . '/tests/resources/' . $this->base . '/' . __FUNCTION__ . '.raw'), $result);
    }

    public function testList()
    {
        $element = DeliveryNotes::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->list('0000001', false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testInfo()
    {
        $element = DeliveryNotes::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->info('CI-2024-0000062', false);
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

        $element = DeliveryNotes::mount($connexion, $this->log, $this->cache);

        $result = $element->img('CI-2024-0000062', false);
        $this->assertIsString($result);
        $this->assertEquals(file_get_contents(getcwd() . '/tests/resources/' . $this->base . '/' . __FUNCTION__ . '.raw'), $result);
    }
}
