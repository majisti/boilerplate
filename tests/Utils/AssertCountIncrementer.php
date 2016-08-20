<?php

namespace Tests\Utils;

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
