<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\Deck;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Deck uut()
 */
class DeckTest extends UnitTest
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Deck();
    }

    private function addManyCards(int $count) {
        for ($i = 0; $i < $count; $i++) {
            $this->uut()->add(new Card());
        }
    }

    public function testIsArrayCollection()
    {
        $this->verifyThat($this->uut(), is(anInstanceOf(ArrayCollection::class)));
    }

    public function testCanDrawCardsAtTheTopOfThePile()
    {
        $this->addManyCards(9);
        $this->uut()->add($expectedDrawnCard = new Card(Card::SUIT_HEARTS, 3));

        $this->verifyThat($this->uut()->count(), equalTo(10));
        $card = $this->uut()->draw();

        $this->verifyThat($this->uut()->count(), equalTo(9));
        $this->verifyThat($card, is(anInstanceOf(Card::class)));
        $this->verifyThat($card, is(sameInstance($expectedDrawnCard)));

        $this->uut()->draw();
        $this->verifyThat($this->uut()->count(), equalTo(8));
    }

    public function testWillReturnNullWhenDrawingIfDeckIsEmpty()
    {
        $this->verifyThat($this->uut()->draw(), is(nullValue()));
    }
}
