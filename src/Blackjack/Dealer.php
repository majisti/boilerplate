<?php

namespace Blackjack;

class Dealer
{
    /**
     * @var Deck
     */
    private $deck;

    /**
     * @var Hand
     */
    private $hand;

    public function __construct(Deck $deck)
    {
        $this->deck = $deck;
        $this->hand = new Hand();
    }
    
    public function giveCardToPlayer(Player $player)
    {
        $this->giveCardsToPlayer($player, 1);
    }

    public function giveCardsToPlayer(Player $player, int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $player->receiveCard($this->deck->draw());
        }
    }

    public function drawManyCards(int $count): Hand
    {
        for ($i = 0; $i < $count; $i++) {
            $this->hand->add($this->deck->draw());
        }
        
        return $this->getHand();
    }
    
    public function getHand(): Hand
    {
        return $this->hand;
    }
}