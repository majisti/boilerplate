<?php

namespace Blackjack;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @method Card[] toArray()
 * @method Card[] slice($offset, $length = null)
 */
class Deck extends ArrayCollection
{
    /**
     * @param Card[] $cards
     */
    public function addCards(array $cards)
    {
        foreach ($cards as $card) {
            $this->add($card);
        }
    }

    /**
     * @return Card|null
     */
    public function draw()
    {
        return $this->remove($this->count() - 1);
    }
}
