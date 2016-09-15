<?php

namespace Unit\Blackjack;

use Blackjack\Hand;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Hand uut()
 */
class HandTest extends UnitTest
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Hand();
    }

    public function testIsArrayCollection()
    {
        $this->verifyThat($this->uut(), is(anInstanceOf(ArrayCollection::class)));
    }

    public function testScoresShouldBeZeroWhenNeverSet()
    {
        $this->verifyThat($this->uut()->getScore(), equalTo(0));
        $this->verifyThat($this->uut()->getAlternateScore(), equalTo(0));
        $this->verifyThat($this->uut()->hasAlternateScore(), is(false));
    }
}
