<?php

namespace Tests\Codeception\TestCase;

use Faker\Factory;
use Faker\Generator;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
trait Faker
{
    protected $faker;

    protected function getFaker(): Generator
    {
        if (null === $this->faker) {
            $this->faker = Factory::create();
        }

        return $this->faker;
    }
}
