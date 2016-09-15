<?php

namespace Blackjack;

class Deck extends CardCollection
{
    /**
     * @return Card|null
     */
    public function draw()
    {
        return $this->cards->remove($this->count() - 1);
    }
}
