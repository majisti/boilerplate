<?php

namespace Blackjack;

use Blackjack\Event\PlayerTurnEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Player extends BlackjackPlayer
{
    public function hit(Dealer $dealer)
    {
        $dealer->hit($this);
    }

    public function stand(EventDispatcher $dispatcher)
    {
        $this->endOfTurn($dispatcher);
    }

    public function endOfTurn(EventDispatcher $dispatcher)
    {
        $dispatcher->dispatch(PlayerTurnEvent::END_OF_TURN, new PlayerTurnEvent($this));
    }
}
