<?php

namespace Unit\Blackjack;

use Blackjack\Dealer;
use Blackjack\Game;
use Blackjack\Player;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Game uut()
 *
 * @property Dealer|m\MockInterface dealer
 * @property Player|m\MockInterface player
 */
class GameTest extends UnitTest
{
    protected $uut;

    protected function setUp()
    {
        $this->dealer = m::spy(Dealer::class);
        $this->player = m::spy(Player::class);

        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        $game = new Game();
        $game->setDealer($this->dealer);
        $game->setPlayer($this->player);

        return $game;
    }

    public function testInitializingGameWillDistributeCardsToParties()
    {
        $this->dealer->shouldReceive('drawMany')
            ->once()
            ->with(2);

        $this->dealer->shouldReceive('hit')
            ->once()
            ->with($this->player, 2);

        $this->uut()->initialize();
    }

    public function testTellsIfDealerWon()
    {
        $this->uut()->dealerWins();
        $this->verifyThat($this->uut()->hasDealerWon(), is(true));
        $this->verifyThat($this->uut()->hasPlayerWon(), is(false));
    }

    public function testTellsIfGameIsADraw()
    {
        $this->uut()->setIsDraw();
        $this->verifyThat($this->uut()->isDraw(), is(true));
    }

    public function testTellsIfPlayerWon()
    {
        $this->uut()->playerWins();
        $this->verifyThat($this->uut()->hasPlayerWon(), is(true));
        $this->verifyThat($this->uut()->hasDealerWon(), is(false));
    }

    public function testWinResultEmpty()
    {
        $this->verifyThat($this->uut()->hasDealerWon(), is(false));
        $this->verifyThat($this->uut()->hasPlayerWon(), is(false));
        $this->verifyThat($this->uut()->isDraw(), is(false));
    }
}
