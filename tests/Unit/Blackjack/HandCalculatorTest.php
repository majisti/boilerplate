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

    protected function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new HandCalculator();
    }
    
    public function testCanCalculateABlackJack()
    {
        $hand = new Hand();
        $hand->addCards([new Card(Card::RANK_KING), new Card(Card::RANK_ACE)]);

        $this->uut()->calculate($hand);

        $this->verifyThat($hand->getBestScore(), equalTo(21));
    }

    public function testCanCalculateScoreForHandContainingAnAce()
    {
        $hand = new Hand();
        $hand->addCards([new Card(Card::RANK_ACE), new Card(Card::RANK_ACE), new Card(5)]);

        $this->uut()->calculate($hand);

        $this->verifyThat($hand->getBestScore(), equalTo(17));
        $this->verifyThat($hand->getAlternativeScore(), equalTo(7));
    }

    public function testCanCalculateScoreForSimpleHand()
    {
        $hand = new Hand();
        $hand->addCards([new Card(2), new Card(5)]);

        $this->uut()->calculate($hand);
        
        $this->verifyThat($hand->getBestScore(), equalTo(7));
        $this->verifyThat($hand->hasAlternateScore(), is(false));
    }
}
