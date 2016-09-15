<?php

namespace Blackjack;

class HandCalculator
{
    public function calculate(Hand $hand)
    {
        $score = 0;
        foreach ($hand->toArray() as $card) {
            $score += Card::estimateScoreValue($card);
        }

        $hand->setScore($score);

        if ($hand->hasAce()) {
            $hand->setAlternateScore($score + 10);
        }
    }
}
