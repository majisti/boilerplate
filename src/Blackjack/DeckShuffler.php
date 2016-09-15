<?php

namespace Blackjack;

class DeckShuffler
{
    public function shuffle(Deck $deck): Deck
    {
        $cards = $deck->toArray();
        shuffle($cards);

        return new Deck($cards);
    }
}
