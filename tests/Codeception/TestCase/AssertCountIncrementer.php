<?php

namespace Tests\Codeception\TestCase;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
trait AssertCountIncrementer
{
    protected function incrementAssertionCounterByOne()
    {
        \PHPUnit_Framework_Assert::assertTrue(true);
    }
}
