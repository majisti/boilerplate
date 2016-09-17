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

    public function testReturnsBestScores()
    {
        $this->player->shouldReceive('getBestScore')->once()->andReturn(3);
        $this->dealer->shouldReceive('getBestScore')->once()->andReturn(5);

        $this->verifyThat($this->uut()->getPlayerBestScore(), is(equalTo(3)));
        $this->verifyThat($this->uut()->getDealerBestScore(), is(equalTo(5)));
    }

    public function testWillTellPlayerHeWins()
    {
        $this->player->shouldReceive('wins')->once();
        $this->dealer->shouldReceive('loses')->once();

        $this->uut()->playerWins();
    }

    public function testWillTellDealerHeWins()
    {
        $this->player->shouldReceive('loses')->once();
        $this->dealer->shouldReceive('wins')->once();

        $this->uut()->dealerWins();
    }
}
