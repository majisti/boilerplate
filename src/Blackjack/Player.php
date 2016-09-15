<?php

namespace Blackjack;

class Player
{
    /**
     * @var Hand
     */
    private $hand;

    public function __construct()
    {
        $this->hand = new Hand();
    }

    public function receiveCard(Card $card)
    {
        $this->hand->add($card);
    }

    public function getHand(): Hand
    {
        return $this->hand;
    }
}