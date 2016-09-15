<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\Hand;
use Blackjack\HandCalculator;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method HandCalculator uut()
 */
class HandCalculatorTest extends UnitTest
{
    protected $uut;

    public function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new HandCalculator();
    }
    
    public function testCanCalculateScoreForSimpleHand()
    {
        $hand = new Hand();
        $hand->add(new Card(Card::SUIT_HEARTS, 2));
        $hand->add(new Card(Card::SUIT_HEARTS, 5));
        
        $this->uut()->calculateForHand($hand);
        
        $this->verifyThat($hand->getScore(), equalTo(7));
        $this->verifyThat($hand->hasAlternateScore(), is(false));
    }
}
