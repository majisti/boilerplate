<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\Deck;
use Blackjack\DeckShuffler;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method DeckShuffler uut()
 */
class DeckShufflerTest extends UnitTest
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new DeckShuffler();
    }

    public function testCanShuffleADeck()
    {
        $unShuffledDeck = new Deck();
        $unShuffledCards = [];

        for ($i = 0; $i < 50; $i++) {
            $card = Card::createRandom();

            $unShuffledCards[] = $card;
            $unShuffledDeck->addCard($card);
        }

        $shuffledDeck = $this->uut()->shuffle($unShuffledDeck);

        $this->verifyThat($unShuffledDeck, is(not(sameInstance($shuffledDeck))));
        $this->verifyThat(count($unShuffledCards), equalTo($shuffledDeck->count()));
        $this->verifyThat($shuffledDeck->toArray(), is(not(equalTo($unShuffledCards))));
    }
}
