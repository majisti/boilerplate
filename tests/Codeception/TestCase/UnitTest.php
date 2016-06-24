<?php

namespace Tests\Codeception\TestCase;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;
use Tests\UnitTester;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
abstract class UnitTest extends PHPUnit_Framework_TestCase
{
    use Hamcrest;
    use MockeryPHPUnitIntegration;

    /**
     * The Unit Under Test.
     */
    protected $uut;

    /**
     * @var UnitTester
     */
    protected $tester;

    public function setUp()
    {
        parent::setUp();
        $this->uut = $this->createUnitUnderTest();
    }

    /**
     * @return mixed
     */
    protected function createUnitUnderTest()
    {
    }
}
