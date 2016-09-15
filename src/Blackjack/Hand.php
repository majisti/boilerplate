<?php

namespace Blackjack;

class Hand extends CardCollection
{
    const MAXIMUM_SCORE = 21;

    private $score = 0;
    private $alternateScore = 0;

    public function hasBlackjack(): bool
    {
        if ($this->count() == 2) {
            return $this->hasAce() && $this->hasTen();
        }

        return false;
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

    public function hasBusted(): bool
    {
        return $this->getBestScore() > static::MAXIMUM_SCORE
        && ($this->getAlternativeScore() > static::MAXIMUM_SCORE || !$this->hasAlternateScore());
    }

    public function getBestScore(): int
    {
        $bestScore = $this->score;

        if ($bestScore < $this->alternateScore && $this->alternateScore <= static::MAXIMUM_SCORE) {
            $bestScore = $this->alternateScore;
        }

        return $bestScore;
    }

    public function getAlternativeScore(): int
    {
        return $this->getBestScore() === $this->score
            ? $this->alternateScore
            : $this->score;
    }

    public function hasAlternateScore()
    {
        return $this->alternateScore > 0;
    }

    public function setAlternateScore(int $score)
    {
        $this->alternateScore = $score;
    }

    public function setScore(int $score)
    {
        $this->score = $score;
    }
}
