<?php

namespace Blackjack\Event;

use Blackjack\Player;
use Symfony\Component\EventDispatcher\Event;

class PlayerTurnEvent extends Event
{
    const END_OF_TURN = 'turn.end';

    /**
     * @var Player
     */
    private $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }
}
