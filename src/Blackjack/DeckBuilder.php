<?php

namespace Blackjack;

class DeckBuilder
{
    /**
     * @var Card[]
     */
    private $cards = [];

    /**
     * @var DeckShuffler
     */
    private $shuffler;

    public function addAllCards(): DeckBuilder
    {
        foreach (Card::SUITS as $suit) {
            for ($rank = 1; $rank <= Card::CARDS_PER_TYPE_COUNT; ++$rank) {
                $this->cards[] = new Card($rank, $suit);
            }
        }

        return $this;
    }

    public function getDeck(): Deck
    {
        return new Deck($this->cards);
    }

    public function shuffle(): DeckBuilder
    {
        $this->cards = $this->getShuffler()
            ->shuffle(new Deck($this->cards))
            ->toArray();

        return $this;
    }

    public function getShuffler(): DeckShuffler
    {
        if (null === $this->shuffler) {
            $this->shuffler = new DeckShuffler();
        }

        return $this->shuffler;
    }

    public function setShuffler(DeckShuffler $shuffler)
    {
        $this->shuffler = $shuffler;
    }
}
