<?php

use PHPUnit\Framework\TestCase;
use Raorsa\SageMiddlewareClient\Jobs;
use Tests\Traits\baseTest;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Jobs::class)]
#[CoversFunction('list')]
#[CoversFunction('listReport')]
#[CoversFunction('operations')]
#[CoversFunction('operationsJobs')]
#[CoversFunction('info')]
#[CoversFunction('getSN')]
#[CoversFunction('importLog')]
class JobsTest extends TestCase
{
    use baseTest;

    protected string $base = 'jobs';


    public function testOperationsJobs()
    {
        $jobs = Jobs::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $jobs->operationsJobs(false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testImportLog()
    {
        $jobs = Jobs::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $jobs->importLog('2024-04-30', false);
        $this->assertIsArray($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testListReport()
    {
        $jobs = Jobs::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $jobs->listReport(false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testInfo()
    {
        $jobs = Jobs::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $jobs->info('2024-241', false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testList()
    {
        $jobs = Jobs::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $jobs->list(false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testOperations()
    {
        $jobs = Jobs::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $jobs->operations(false);
        $this->assertIsObject($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }

    public function testGetSN()
    {
        $jobs = Jobs::mount($this->createConnexion(__FUNCTION__), $this->log, $this->cache);

        $result = $jobs->getSN('2024-166', false);
        $this->assertIsString($result);
        $this->assertEquals(json_decode($this->getData(__FUNCTION__)), $result);
    }
}
