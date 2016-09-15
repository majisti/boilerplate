<?php

namespace Blackjack;

class BlackjackPlayer
{
    /**
     * @var Hand
     */
    protected $hand;

    public function __construct()
    {
        $this->hand = new Hand();
    }

    public function receiveCard(Card $card)
    {
        $this->hand->add($card);
    }

    public function getCards()
    {
        return $this->getHand()->toArray();
    }

    public function getBestScore(): int
    {
        return $this->getHand()->getBestScore();
    }

    public function hasAlternativeScore(): bool
    {
        return $this->getHand()->hasAlternateScore();
    }

    public function getAlternativeScore(): int
    {
        return $this->getHand()->getAlternativeScore();
    }

    public function getHand(): Hand
    {
        return $this->hand;
    }

    public function hasBlackjack()
    {
        return $this->getHand()->hasBlackjack();
    }

    public function hasBusted(): bool
    {
        $hand = $this->getHand();

        return $hand->getBestScore() > Hand::MAXIMUM_SCORE
            && ($hand->getAlternativeScore() > Hand::MAXIMUM_SCORE || !$hand->hasAlternateScore())
        ;
    }

    public function calculateHand(HandCalculator $handCalculator)
    {
        $handCalculator->calculate($this->getHand());
    }

    public function setHand(Hand $hand)
    {
        $this->hand = $hand;
    }
}
