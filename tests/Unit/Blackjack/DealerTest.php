<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\Dealer;
use Blackjack\Deck;
use Blackjack\Hand;
use Blackjack\HandCalculator;
use Blackjack\Player;
use Mockery as m;

/**
 * @method Dealer uut()
 *
 * @property Deck|m\MockInterface deck
 * @property Player|m\MockInterface player
 */
class DealerTest extends PlayerTest
{
    protected function setUp()
    {
        $this->player = m::mock(Player::class);

        $this->deck = m::mock(Deck::class);
        $this->deck->shouldReceive('draw')->andReturn(new Card())->byDefault();

        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Dealer($this->deck);
    }

    public function testCanGiveCardsToHimself()
    {
        $hand = $this->uut()->drawMany(2);

        $this->verifyThat($hand, is(anInstanceOf(Hand::class)));
        $this->verifyThat($hand->count(), is(equalTo(2)));
    }

    public function testCanGiveCardsToPlayer()
    {
        $player = m::mock(Player::class);
        $player->shouldReceive('receiveCard')
            ->times(2)
            ->with(anInstanceOf(Card::class));

        $this->uut()->hit($player, 2);
    }

    public function testDealerCannotDrawIfHeHasMoreThanSixteen()
    {
        $handCalculator = new HandCalculator();

        $this->deck->shouldReceive('draw')->once()->andReturn(new Card(1));
        $this->uut()->receiveCards([
            new Card(10),
            new Card(6),
        ]);
        $this->uut()->calculateHand($handCalculator);

        $this->player->shouldReceive('getBestScore')->andReturn(18);

        $this->uut()->outplay($this->player, $handCalculator);
        $this->verifyThat($this->uut()->getBestScore(), equalTo(17));
    }

    public function testDealerTriesToBeatPlayer()
    {
        $this->deck->shouldReceive('draw')->andReturn(new Card(2));

        $this->player->shouldReceive('getBestScore')->andReturn(17);
        $this->uut()->outplay($this->player, new HandCalculator());

        $this->verifyThat($this->uut()->getBestScore(), equalTo(18));
    }

    public function testKnowsIfMustContinueToDraw()
    {
        $hand = m::mock(Hand::class);
        $hand->shouldReceive('getBestScore')->andReturn(16, 17);
        $this->uut()->setHand($hand);

        $this->verifyThat($this->uut()->hasToDraw(), is(true));
        $this->verifyThat($this->uut()->hasToDraw(), is(false));
    }

    //todo: dealer can hit on a soft 17? Maybe add it as an optional rule (Rulebook object?)
}
