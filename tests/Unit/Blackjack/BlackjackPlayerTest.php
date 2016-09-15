<?php

namespace Unit\Blackjack;

use Blackjack\BlackjackPlayer;
use Blackjack\Card;
use Blackjack\Hand;
use Blackjack\HandCalculator;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method BlackjackPlayer uut()
 */
abstract class BlackjackPlayerTest extends UnitTest
{
    public function setUp()
    {
        parent::setUp();
    }
    
    protected function receiveManyCards(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->uut()->receiveCard(new Card());
        }
    }

    public function testReceivingCardAddsItToHisHand()
    {
        $this->receiveManyCards(3);

        $hand = $this->uut()->getHand();
        $this->verifyThat($hand->count(), equalTo(3));
    }

    public function testWillUseHandCalculatorToCalculateHand()
    {
        $handCalculator = m::mock(HandCalculator::class);
        $handCalculator->shouldReceive('calculate')->with(anInstanceOf(Hand::class));
        
        $this->uut()->calculateHand($handCalculator);
    }

    public function testReportHandHasBusted()
    {
        $this->verifyThat($this->uut()->hasBusted(), is(false));

        $hand = $this->uut()->getHand();

        $hand->setScore(22);
        $hand->setAlternateScore(23);
        $this->verifyThat($this->uut()->hasBusted(), is(true));

        $hand->setScore(22);
        $hand->setAlternateScore(0);
        $this->verifyThat($this->uut()->hasBusted(), is(true));

        $hand->setScore(22);
        $hand->setAlternateScore(11);
        $this->verifyThat($this->uut()->hasBusted(), is(false));
    }
    
    public function testCanTellIfItHasABlackJack()
    {
        $hand = m::mock(Hand::class);
        $hand->shouldReceive('hasBlackjack')->andReturn(true, false);
        
        $this->uut()->setHand($hand);
        
        $this->verifyThat($this->uut()->hasBlackjack(), equalTo(true));
        $this->verifyThat($this->uut()->hasBlackjack(), equalTo(false));
    }
}
