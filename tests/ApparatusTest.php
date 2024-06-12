<?php


use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Raorsa\SageMiddlewareClient\Apparatus;
use Tests\Traits\baseTest;

#[CoversClass(Apparatus::class)]
#[CoversFunction('list')]
#[CoversFunction('interventions')]
#[CoversFunction('integrated')]
#[CoversFunction('brandInfo')]
#[CoversFunction('brands')]
#[CoversFunction('types')]
#[CoversFunction('warranties')]
#[CoversFunction('interventionsType')]
class ApparatusTest extends TestCase
{
    use baseTest;

    protected string $base = 'apparatus';

    public function testTypes()
    {
        $element = Apparatus::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->types(false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testBrandInfo()
    {
        $element = Apparatus::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->brandInfo(17, false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testWarranties()
    {
        $element = Apparatus::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->warranties(false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testInterventionsType()
    {
        $element = Apparatus::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->interventionsType(false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testBrands()
    {
        $element = Apparatus::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->brands(false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testIntegrated()
    {
        $element = Apparatus::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->integrated(1539, false);
        $this->assertIsArray($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testInterventions()
    {
        $element = Apparatus::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->interventions('0000270', false);
        $this->assertIsArray($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testList()
    {
        $element = Apparatus::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->list('0000270', false);
        $this->assertIsArray($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }
}
