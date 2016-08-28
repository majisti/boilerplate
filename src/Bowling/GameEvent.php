<?php

namespace Bowling;

class GameEvent
{
    /**
     * @var Game
     */
    protected $game;

    /**
     * @var Roll
     */
    private $roll;

    /**
     * @var Frame
     */
    private $frame;

    public function __construct(Game $game, Frame $frame = null, Roll $roll = null)
    {
        $this->game = $game;
        $this->roll = $roll;
        $this->frame = $frame;
    }

    public function hasFrame(): bool
    {
        return null !== $this->getFrame();
    }

    public function hasRoll(): bool
    {
        return null !== $this->getRoll();
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function getFrame(): Frame
    {
        return $this->frame;
    }

    public function getRoll(): Roll
    {
        return $this->roll;
    }
}
