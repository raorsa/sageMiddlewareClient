<?php


use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Raorsa\SageMiddlewareClient\Articles;
use Tests\Traits\baseTest;


#[CoversClass(Articles::class)]
#[CoversFunction('screwTips')]
#[CoversFunction('findScrewTips')]
#[CoversFunction('searchScrewTips')]
class ArticlesTest extends TestCase
{
    use baseTest;

    protected string $base = 'articles';

    public function testScrewTips()
    {
        $element = Articles::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->screwTips(false);
        $this->assertIsArray($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testSearchScrewTips()
    {
        $element = Articles::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->searchScrewTips([35], false);
        $this->assertIsArray($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testFindScrewTips()
    {
        $element = Articles::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $element->findScrewTips(35, false);
        $this->assertIsArray($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }
}
