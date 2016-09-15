<?php

namespace Blackjack;

class HandCalculator
{
    public function calculateForHand(Hand $hand)
    {
        //todo: complete this calculation
        
        $score = 0;
        foreach ($hand->toArray() as $card) {
            $score += $card->getRank();
        }
        
        $hand->setScore($score);
    }
}