<?php

namespace Bowling;

use Symfony\Component\EventDispatcher\Event;

class GameEvent extends Event
{
    const EVENT_NEW_ROLL = 'game.new-roll';
    const EVENT_NEW_FRAME = 'game.new-frame';

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

    /**
     * @return Frame|null
     */
    public function getFrame()
    {
        return $this->frame;
    }

    /**
     * @return Roll|null
     */
    public function getRoll()
    {
        return $this->roll;
    }
}
