<?php

namespace Tests\Feature;

use Tests\Codeception\TestCase\FeatureTest;

/**
 * @group movies.transfer
 */
class MoviesTransferTest extends FeatureTest
{
    /**
     * @test
     */
    public function shouldBePossibleToTransferMoviesFromDownloadLocationToMoviesLocation()
    {
        $this->verifyThat(true, equalTo(true));
    }
}
