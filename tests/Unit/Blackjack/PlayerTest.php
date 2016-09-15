<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\Dealer;
use Blackjack\Event\PlayerTurnEvent;
use Blackjack\Player;
use Mockery as m;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @method Player uut()
 */
class PlayerTest extends BlackjackPlayerTest
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Player();
    }
    
    public function testCanCallHitToDealer()
    {
        $dealer = m::mock(Dealer::class);
        $dealer->shouldReceive('hit')->once()->with($this->uut());

        $this->uut()->hit($dealer);
    }
    
    public function testCanStandByEndingHisTurn()
    {
        $dispatcher = $this->getEndOfTurnMock();
        $this->uut()->stand($dispatcher);
    }

    public function testCanEndTurn()
    {
        $dispatcher = $this->getEndOfTurnMock();
        $this->uut()->endOfTurn($dispatcher);
    }

    /**
     * @return m\MockInterface|EventDispatcher
     */
    private function getEndOfTurnMock()
    {
        $dispatcher = m::mock(EventDispatcher::class);
        $dispatcher->shouldReceive('dispatch')->once()
            ->with(PlayerTurnEvent::END_OF_TURN, PlayerTurnEvent::class);
        return $dispatcher;
    }
}
