<?php

namespace Blackjack;

class Dealer extends BlackjackPlayer
{
    const DEALER_MINIMUM_SCORE = 17;

    /**
     * @var Deck
     */
    private $deck;

    public function __construct(Deck $deck)
    {
        $this->deck = $deck;
        parent::__construct();
    }

    public function hit(Player $player, int $count = 1)
    {
        for ($i = 0; $i < $count; ++$i) {
            $player->receiveCard($this->deck->draw());
        }
    }

    public function drawManyCards(int $count): Hand
    {
        for ($i = 0; $i < $count; ++$i) {
            $this->receiveCard($this->deck->draw());
        }

        return $this->getHand();
    }

    public function hasToDraw(): bool
    {
        return $this->getBestScore() < static::DEALER_MINIMUM_SCORE;
    }
}
