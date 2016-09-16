<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\CardCollection;
use Blackjack\Deck;
use Tests\Unit\UnitTest;

/**
 * @method Deck uut()
 */
class DeckTest extends UnitTest
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Deck();
    }

    public function testIsCardCollection()
    {
        $this->verifyThat($this->uut(), is(anInstanceOf(CardCollection::class)));
    }

    public function testCanDrawCardsAtTheTopOfThePile()
    {
        $this->addManyCards(9);
        $this->uut()->addCard($expectedDrawnCard = new Card(3));

        $this->verifyThat($this->uut()->count(), equalTo(10));
        $card = $this->uut()->draw();

        $this->verifyThat($this->uut()->count(), equalTo(9));
        $this->verifyThat($card, is(anInstanceOf(Card::class)));
        $this->verifyThat($card, is(sameInstance($expectedDrawnCard)));

        $this->uut()->draw();
        $this->verifyThat($this->uut()->count(), equalTo(8));
    }

    private function addManyCards(int $count)
    {
        for ($i = 0; $i < $count; ++$i) {
            $this->uut()->addCard(new Card());
        }
    }

    public function testWillReturnNullWhenDrawingIfDeckIsEmpty()
    {
        $this->verifyThat($this->uut()->draw(), is(nullValue()));
    }
}
