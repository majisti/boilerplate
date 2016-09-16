<?php

namespace Unit\Blackjack;

use Blackjack\Dealer;
use Blackjack\Game;
use Blackjack\Player;
use Blackjack\WinnerDeterminer;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method WinnerDeterminer uut()
 *
 * @property Dealer|m\MockInterface $dealer
 * @property Player|m\MockInterface $player
 * @property Game|m\MockInterface game
 */
class WinnerDeterminerTest extends UnitTest
{
    protected function setUp()
    {
        $this->game = m::spy(new Game());
        $this->dealer = m::spy(Dealer::class);
        $this->player = m::spy(Player::class);

        $this->game->setDealer($this->dealer);
        $this->game->setPlayer($this->player);

        $this->game->shouldReceive('setIsDraw')->never()->byDefault();

        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new WinnerDeterminer();
    }

    public function testCanDetermineWinner()
    {
        $this->dealer->shouldReceive('getBestScore')->andReturn(18);
        $this->player->shouldReceive('getBestScore')->andReturn(15);

        $this->game->shouldReceive('dealerWins')->once();
        $this->uut()->determine($this->game);
    }

    public function testDealerAndPlayerBlackjackIsADraw()
    {
        $this->dealer->shouldReceive('hasBlackjack')->andReturn(true);
        $this->player->shouldReceive('hasBlackjack')->andReturn(true);

        $this->game->shouldReceive('setIsDraw')->once();
        $this->uut()->determine($this->game);
    }

    public function testDealerBlackjackWins()
    {
        $this->dealer->shouldReceive('hasBlackjack')->andReturn(true);
        $this->player->shouldReceive('getBestScore')->andReturn(18);

        $this->game->shouldReceive('dealerWins')->once();
        $this->uut()->determine($this->game);
    }

    public function testEqualScoreIsADraw()
    {
        $this->dealer->shouldReceive('getBestScore')->andReturn(17);
        $this->player->shouldReceive('getBestScore')->andReturn(17);

        $this->game->shouldReceive('setIsDraw')->once();
        $this->uut()->determine($this->game);
    }

    public function testPlayerBlackjackWins()
    {
        $this->dealer->shouldReceive('getBestScore')->andReturn(18);
        $this->player->shouldReceive('hasBlackjack')->andReturn(true);

        $this->game->shouldReceive('playerWins')->once();
        $this->uut()->determine($this->game);
    }

    public function testPlayerWhoBustsLoses()
    {
        $this->dealer->shouldReceive('getBestScore')->andReturn(18);
        $this->player->shouldReceive('getBestScore')->andReturn(23);
        $this->player->shouldReceive('hasBusted')->andReturn(true);

        $this->game->shouldReceive('dealerWins')->once();
        $this->uut()->determine($this->game);
    }
}
