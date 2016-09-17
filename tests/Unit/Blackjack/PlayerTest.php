<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\Hand;
use Blackjack\HandCalculator;
use Blackjack\Player;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Player uut()
 */
class PlayerTest extends UnitTest
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Player();
    }

    public function testCanTellIfItHasABlackJack()
    {
        $hand = m::mock(Hand::class);
        $hand->shouldReceive('hasBlackjack')->andReturn(true, false);

        $this->uut()->setHand($hand);

        $this->verifyThat($this->uut()->hasBlackjack(), equalTo(true));
        $this->verifyThat($this->uut()->hasBlackjack(), equalTo(false));
    }

    public function testReceivingCardAddsItToHisHand()
    {
        $this->receiveManyCards(3);

        $hand = $this->uut()->getHand();
        $this->verifyThat($hand->count(), equalTo(3));
    }

    protected function receiveManyCards(int $count)
    {
        for ($i = 0; $i < $count; ++$i) {
            $this->uut()->receiveCard(new Card());
        }
    }

    public function testWillUseHandCalculatorToCalculateHand()
    {
        $handCalculator = m::mock(HandCalculator::class);
        $handCalculator->shouldReceive('calculate')->with(anInstanceOf(Hand::class));

        $this->uut()->calculateHand($handCalculator);
    }

    public function testCanTrackHisNumberOfWinsAndLosses()
    {
        $this->verifyThat($this->uut()->getWinsCount(), equalTo(0));
        $this->verifyThat($this->uut()->getLossesCount(), equalTo(0));

        $this->uut()->wins();
        $this->verifyThat($this->uut()->getWinsCount(), equalTo(1));
        $this->verifyThat($this->uut()->getLossesCount(), equalTo(0));

        $this->uut()->loses();
        $this->verifyThat($this->uut()->getWinsCount(), equalTo(1));
        $this->verifyThat($this->uut()->getLossesCount(), equalTo(1));
    }

    public function testCanResetHand()
    {
        $lastHand = new Hand();
        $this->uut()->setHand($lastHand);

        $this->uut()->resetHand();
        $this->verifyThat($this->uut()->getHand(), is(not(sameInstance($lastHand))));
    }
}
