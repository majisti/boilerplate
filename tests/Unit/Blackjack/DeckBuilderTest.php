<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\DeckShuffler;
use Blackjack\Deck;
use Blackjack\DeckBuilder;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method DeckBuilder uut()
 */
class DeckBuilderTest extends UnitTest
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new DeckBuilder();
    }

    public function testBuildsFullStandardDeck()
    {
        $this->uut()->addAllCards();
        $deck = $this->uut()->getDeck();

        $this->verifyThat($deck, is(anInstanceOf(Deck::class)));
        $this->verifyThat($deck->count(), equalTo(52));

        foreach (Card::SUITS as $typeIndex => $type) {
            $cards = $deck->slice($typeIndex * Card::CARDS_PER_TYPE_COUNT, Card::CARDS_PER_TYPE_COUNT);

            foreach ($cards as $card) {
                $this->verifyThat($card->getSuit(), equalTo(Card::SUITS[$typeIndex]));
            }
        }
    }

    public function testCanShuffleDeck()
    {
        $expectedDeck = new Deck();
        $expectedDeck->addCard(new Card());

        $shuffler = m::mock(DeckShuffler::class);
        $shuffler->shouldReceive('shuffle')
            ->once()
            ->with(anInstanceOf(Deck::class))
            ->andReturn($expectedDeck);

        $this->uut()->setShuffler($shuffler);
        $this->uut()->addAllCards();
        $deck = $this->uut()->getDeck();

        $this->uut()->shuffle();
        $shuffledDeck = $this->uut()->getDeck();

        $this->verifyThat($shuffledDeck, is(not(sameInstance($deck))));
        $this->verifyThat($expectedDeck->count(), is(equalTo($shuffledDeck->count())));
    }

    public function testStartOver()
    {
        $deck = $this->uut()
            ->addAllCards()
            ->startOver()
            ->addAllCards()
            ->getDeck();

        $this->verifyThat($deck->count(), equalTo(52));
    }
}
