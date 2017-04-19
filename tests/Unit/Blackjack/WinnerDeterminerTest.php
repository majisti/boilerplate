<?php

namespace Unit\Blackjack;

use Blackjack\Dealer;
use Blackjack\Player;
use Blackjack\WinnerDeterminer;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method WinnerDeterminer uut()
 *
 * @property Dealer|m\MockInterface $dealer
 * @property Player|m\MockInterface $player
 */
class WinnerDeterminerTest extends UnitTest
{
    protected function setUp()
    {
        $this->dealer = m::spy(Dealer::class);
        $this->player = m::spy(Player::class);

        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new WinnerDeterminer();
    }

    public function testDealerIsWinnerOnBestScore()
    {
        $this->dealer->shouldReceive('getBestScore')->andReturn(18);
        $this->player->shouldReceive('getBestScore')->andReturn(15);

        $this->verifyThat($this->uut()->determine($this->dealer, $this->player)->getWinner(), equalTo($this->dealer));
    }

    public function testDealerAndPlayerBlackjackIsADraw()
    {
        $this->dealer->shouldReceive('hasBlackjack')->andReturn(true);
        $this->player->shouldReceive('hasBlackjack')->andReturn(true);

        $this->verifyThat($this->uut()->determine($this->dealer, $this->player)->isDraw(), is(true));
    }

    public function testDealerBlackjackWins()
    {
        $this->dealer->shouldReceive('hasBlackjack')->andReturn(true);
        $this->player->shouldReceive('getBestScore')->andReturn(18);

        $this->verifyThat($this->uut()->determine($this->dealer, $this->player)->getWinner(), equalTo($this->dealer));
    }

    public function testEqualScoreIsADraw()
    {
        $this->dealer->shouldReceive('getBestScore')->andReturn(17);
        $this->player->shouldReceive('getBestScore')->andReturn(17);

        $this->verifyThat($this->uut()->determine($this->dealer, $this->player)->isDraw(), is(true));
    }

    public function testPlayerBlackjackWins()
    {
        $this->dealer->shouldReceive('getBestScore')->andReturn(18);
        $this->player->shouldReceive('hasBlackjack')->andReturn(true);

        $this->verifyThat($this->uut()->determine($this->dealer, $this->player)->getWinner(), equalTo($this->player));
    }

    public function testPlayerWhoBustsLoses()
    {
        $this->dealer->shouldReceive('getBestScore')->andReturn(18);
        $this->player->shouldReceive('getBestScore')->andReturn(23);
        $this->player->shouldReceive('hasBusted')->andReturn(true);

        $this->verifyThat($this->uut()->determine($this->dealer, $this->player)->getWinner(), equalTo($this->dealer));
    }
}
