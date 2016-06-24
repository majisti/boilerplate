<?php

namespace Tests\Unit;

use Tests\Codeception\TestCase\UnitTest;

class AUnitTest extends UnitTest
{
    /**
     * @test
     */
    public function shouldBeTrue()
    {
        $this->verifyThat(true, equalTo(true));
    }
}
