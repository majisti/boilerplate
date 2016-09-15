<?php

namespace Blackjack;

class Player
{
    /**
     * @var Hand
     */
    protected $hand;

    public function __construct()
    {
        $this->hand = new Hand();
    }

    public function calculateHand(HandCalculator $handCalculator)
    {
        $handCalculator->calculate($this->getHand());
    }

    public function getHand(): Hand
    {
        return $this->hand;
    }

    public function setHand(Hand $hand)
    {
        $this->hand = $hand;
    }

    public function getAlternativeScore(): int
    {
        return $this->getHand()->getAlternativeScore();
    }

    public function getBestScore(): int
    {
        return $this->getHand()->getBestScore();
    }

    public function getCards()
    {
        return $this->getHand()->toArray();
    }

    public function hasAlternativeScore(): bool
    {
        return $this->getHand()->hasAlternateScore();
    }

    public function hasBlackjack()
    {
        return $this->getHand()->hasBlackjack();
    }

    public function hasBusted(): bool
    {
        return $this->getHand()->hasBusted();
    }

    /**
     * @param Card[]
     */
    public function receiveCards(array $cards)
    {
        foreach ($cards as $card) {
            $this->receiveCard($card);
        }
    }

    public function receiveCard(Card $card)
    {
        $this->hand->addCard($card);
    }
}
