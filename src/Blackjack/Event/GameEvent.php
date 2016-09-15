<?php

namespace Blackjack\Event;

use Blackjack\Game;
use Symfony\Component\EventDispatcher\Event;

class GameEvent extends Event
{
    const GAME_STARTED = 'game.started';

    /**
     * @var Game
     */
    private $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }
}
