<?php

namespace Tests\Unit;

use Tests\Codeception\TestCase\UnitTest;

class FooTest extends UnitTest
{
    /**
     * @test
     */
    public function shouldBeTrue()
    {
        $this->verifyThat(true, equalTo(true));
    }
}
