<?php

namespace Blackjack;

use Doctrine\Common\Collections\ArrayCollection;

class CardCollection
{
    public function __construct(array $cards = [])
    {
        $this->cards = new ArrayCollection($cards);
    }

    public function count()
    {
        return $this->cards->count();
    }

    public function addCard(Card $card)
    {
        $this->cards->add($card);
    }

    /**
     * @param Card[] $cards
     */
    public function addCards(array $cards)
    {
        foreach ($cards as $card) {
            $this->addCard($card);
        }
    }

    /**
     * @param int $index
     *
     * @return Card|null
     */
    public function getCardAtIndex(int $index)
    {
        return $this->cards->get($index);
    }

    public function prependCards(array $cards)
    {
        foreach ($cards as $card) {
            $cards = $this->cards->toArray();
            array_unshift($cards, $card);

            $this->cards = new ArrayCollection($cards);
        }
    }

    /**
     * @return Card[]
     */
    public function toArray(): array
    {
        return $this->cards->toArray();
    }

    /**
     * @return Card[]
     */
    public function slice(int $offset, $length = null): array
    {
        return array_slice($this->toArray(), $offset, $length);
    }
}
