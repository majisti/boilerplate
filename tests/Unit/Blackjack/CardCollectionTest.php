<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\CardCollection;
use Tests\Unit\UnitTest;

/**
 * @method CardCollection uut()
 */
class CardCollectionTest extends UnitTest
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new CardCollection();
    }

    public function testCanPrependCards()
    {
        $this->uut()->prependCards([$firstCard = new Card(3), $secondCard = new Card(4)]);

        $this->verifyThat($this->uut()->getCardAtIndex(0), is(sameInstance($secondCard)));
        $this->verifyThat($this->uut()->getCardAtIndex(1), is(sameInstance($firstCard)));
    }
}
