<?php

namespace Unit\Bowling;

use Tests\Unit\UnitTest;

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
