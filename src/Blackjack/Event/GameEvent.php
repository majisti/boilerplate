<?php

namespace Blackjack\Event;

use Blackjack\Game;
use Symfony\Component\EventDispatcher\Event;

class GameEvent extends Event
{
    const GAME_STARTED = 'game.started';
    const GAME_ENDED = 'game.ended';

    /**
     * @var Game
     */
    private $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }
}
