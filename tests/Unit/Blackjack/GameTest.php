<?php

namespace Unit\Blackjack;

use Blackjack\Dealer;
use Blackjack\Game;
use Blackjack\Event\GameEvent;
use Blackjack\HandCalculator;
use Blackjack\Player;
use Mockery as m;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\Unit\UnitTest;

/**
 * @method Game uut()
 * @property Dealer|m\MockInterface dealer
 * @property Player|m\MockInterface player
 * @property HandCalculator|m\MockInterface handCalculator
 * @property m\MockInterface|EventDispatcher dispatcher
 */
class GameTest extends UnitTest
{
    protected $uut;

    public function setUp()
    {
        $this->dealer = m::spy(Dealer::class);
        $this->player = m::spy(Player::class);
        $this->handCalculator = m::spy(HandCalculator::class);
        $this->dispatcher = m::spy(EventDispatcher::class);

        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        $game = new Game();
        $game->setDealer($this->dealer);
        $game->setPlayer($this->player);
        $game->setHandCalculator($this->handCalculator);

        return $game;
    }

    public function testStartingGameWillDistributeCardsToParties()
    {
        $this->dealer->shouldReceive('drawManyCards')
            ->once()
            ->with(2);

        $this->dealer->shouldReceive('hit')
            ->once()
            ->with($this->player, 2);

        $this->uut()->initialize();
    }

    public function testDispatchesGameStartedEvent()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(GameEvent::GAME_STARTED, anInstanceOf(GameEvent::class))
        ;
        $this->uut()->setEventDispatcher($this->dispatcher);
        $this->uut()->start();
    }

//    public function testStartingGameWillCalculateHandsForEveryoneAndCheckForAnyBlackJacks()
//    {
//        $this->handCalculator->shouldReceive('calculate')->times(2);
//
//        $this->uut()->start();
//
//        $this->markTestIncomplete();
//    }
}
