<?php

namespace Unit\Blackjack;

use Blackjack\Deck;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Deck uut()
 */
class DeckTest extends UnitTest
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Deck();
    }

    public function testIsCollection()
    {
        $this->verifyThat($this->uut(), is(anInstanceOf(ArrayCollection::class)));
    }
}
