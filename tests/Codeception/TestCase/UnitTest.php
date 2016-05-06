<?php

namespace Tests\Codeception\TestCase;

use Codeception\TestCase\Test;
use Tests\UnitTester;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
abstract class UnitTest extends Test
{
    use Hamcrest;

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
