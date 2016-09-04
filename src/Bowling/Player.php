<?php

namespace Bowling;

class Player
{
    /**
     * @var Game
     */
    private $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function throw(Roll $roll)
    {
        $this->game->addRoll($roll);
    }
}
