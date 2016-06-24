<?php

namespace Tests\Integration;

use Tests\Codeception\TestCase\IntegrationTest;

/**
 * @group integration.test
 */
class AnIntegrationTest extends IntegrationTest
{
    /**
     * @test
     */
    public function shouldBePossibleToDoAnExpectedBehaviour()
    {
        $this->verifyThat(true, equalTo(true));
    }
}
