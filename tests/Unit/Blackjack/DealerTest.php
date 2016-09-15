<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\Dealer;
use Blackjack\Deck;
use Blackjack\Hand;
use Blackjack\Player;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Dealer uut()
 * @property Deck|m\MockInterface deck
 */
class DealerTest extends UnitTest
{
    public function setUp()
    {
        $this->deck = m::mock(Deck::class);
        $this->deck->shouldReceive('draw')->andReturn(new Card())->byDefault();
        
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Dealer($this->deck);
    }
    
    public function testCanGiveCardsToPlayer()
    {
        $player = m::mock(Player::class);
        $player->shouldReceive('receiveCard')
            ->times(2)
            ->with(anInstanceOf(Card::class));
        
        $this->uut()->giveCardsToPlayer($player, 2);
    }

    public function testCanGiveCardsToHimself()
    {
        $hand = $this->uut()->drawManyCards(2);

        $this->verifyThat($hand, is(anInstanceOf(Hand::class)));
        $this->verifyThat($hand->count(), is(equalTo(2)));
    }
}
