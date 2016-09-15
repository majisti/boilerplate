<?php

namespace Blackjack;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @method Card[] toArray()
 */
class Hand extends ArrayCollection
{
    const MAXIMUM_SCORE = 21;

    private $score = 0;
    private $alternateScore = 0;

    public function getBestScore(): int
    {
        return $this->score > $this->alternateScore
            ? $this->score
            : $this->alternateScore;
    }

    public function setScore(int $score)
    {
        $this->score = $score;
    }

    public function getAlternativeScore(): int
    {
        return $this->score < $this->alternateScore
            ? $this->score
            : $this->alternateScore;
    }

    public function setAlternateScore(int $score)
    {
        $this->alternateScore = $score;
    }

    public function hasAlternateScore()
    {
        return $this->alternateScore > 0;
    }

    public function hasAce(): bool
    {
        foreach ($this->toArray() as $card) {
            if ($card->isAce()) {
                return true;
            }
        }

        return false;
    }

    private function hasTen()
    {
        foreach ($this->toArray() as $card) {
            if (Card::estimateScoreValue($card) === 10) {
                return true;
            }
        }

        return false;
    }

    public function hasBlackjack(): bool
    {
        if ($this->count() == 2) {
            return $this->hasAce() && $this->hasTen();
        }

        return false;
    }
}
