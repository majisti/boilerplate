<?php

namespace Blackjack;

use Blackjack\Event\GameEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Game
{
    /**
     * @var Dealer
     */
    private $dealer;

    /**
     * @var Player
     */
    private $player;

    /**
     * @var HandCalculator
     */
    private $handCalculator;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function initialize()
    {
        $dealer = $this->getDealer();

        $dealer->drawManyCards(2);
        $dealer->hit($this->getPlayer(), 2);
    }

    public function start()
    {
        $handCalculator = $this->getHandCalculator();

        $handCalculator->calculate($this->getDealer()->getHand());
        $handCalculator->calculate($this->getPlayer()->getHand());

        $this->dispatch(GameEvent::GAME_STARTED, new GameEvent($this));
    }

    public function getDealer(): Dealer
    {
        return $this->dealer;
    }

    public function setDealer(Dealer $dealer)
    {
        $this->dealer = $dealer;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player)
    {
        $this->player = $player;
    }

    public function getHandCalculator(): HandCalculator
    {
        if (null === $this->handCalculator) {
            $this->handCalculator = new HandCalculator();
        }

        return $this->handCalculator;
    }

    public function setHandCalculator(HandCalculator $handCalculator)
    {
        $this->handCalculator = $handCalculator;
    }

    public function setEventDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    private function dispatch(string $eventName, Event $event)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch($eventName, $event);
        }
    }
}
