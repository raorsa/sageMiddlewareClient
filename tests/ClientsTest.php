<?php


use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Raorsa\SageMiddlewareClient\Clients;
use Tests\Traits\baseTest;


#[CoversClass(Clients::class)]
#[CoversFunction('companyInfo')]
#[CoversFunction('clientInfo')]
#[CoversFunction('validDomains')]
#[CoversFunction('clients')]
class ClientsTest extends TestCase
{
    use baseTest;

    protected string $base = 'clients';

    public function testClients()
    {
        $element = Clients::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->clients(false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testValidDomains()
    {
        $element = Clients::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->validDomains(false);
        $this->assertIsArray($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);

    }

    public function testCompanyInfo()
    {
        $element = Clients::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->companyInfo('raorsa.es', false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testClientInfo()
    {
        $element = Clients::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->clientInfo('0000001', false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);

    }
}
