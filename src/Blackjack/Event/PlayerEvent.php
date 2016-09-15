<?php

namespace Blackjack\Event;

use Blackjack\Player;
use Symfony\Component\EventDispatcher\Event;

class PlayerEvent extends Event
{
    const PLAYER_HIT = 'player.hit';
    const STAND = 'player.stand';
    const DEALER_BLACKJACK = 'dealer.blackjack';
    const PLAYER_BLACKJACK = 'player.blackjack';
    const PLAYER_START_OF_TURN = 'player.turn.start';
    const PLAYER_END_OF_TURN = 'player.turn.end';
    const DEALER_START_OF_TURN = 'dealer.turn.start';
    const DEALER_END_OF_TURN = 'dealer.turn.end';

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
