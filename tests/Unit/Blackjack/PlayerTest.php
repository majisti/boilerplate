<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\Player;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Player uut()
 */
class PlayerTest extends UnitTest
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Player();
    }
    
    public function testReceivingCardAddsItToHisHand()
    {
        $this->receiveManyCards(3);
        
        $hand = $this->uut()->getHand();
        $this->verifyThat($hand->count(), equalTo(3));
    }

    private function receiveManyCards(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->uut()->receiveCard(new Card());
        }
    }
}
