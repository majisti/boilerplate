<?php

namespace Blackjack;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @method Card[] toArray()
 */
class Hand extends ArrayCollection
{
    private $score = 0;
    private $alternateScore = 0;

    public function getScore(): int
    {
        return $this->score;
    }
    
    public function setScore(int $score)
    {
        $this->score = $score;
    }
    
    public function getAlternateScore(): int
    {
        return $this->alternateScore;
    }
    
    public function setAlternateScore(int $score)
    {
        $this->alternateScore = $score;
    }

    public function hasAlternateScore()
    {
        return $this->alternateScore > 0;
    }
}