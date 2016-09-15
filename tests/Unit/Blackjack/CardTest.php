<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Card uut()
 */
class CardTest extends UnitTest
{
    protected $uut;

    public function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Card(Card::SUIT_DIAMONDS, 1);
    }

    public function testCanCreateRandomCard()
    {
        //fixme: sometimes this test fails
        $this->verifyThat($this->uut()->createRandom(), is(anInstanceOf(Card::class)));

        $hasTypeChanged = false;
        $hasValueChanged = false;
        $numberOfTries = 0;
        $lastValue = null;
        $lastType = null;

        while (!$hasTypeChanged && !$hasValueChanged && ++$numberOfTries < 100) {
            $card = $this->uut()->createRandom();

            if ($lastType !== null && $lastType !== $card->getSuit()) {
                $hasTypeChanged = true;
            }

            if ($lastValue !== null && $lastValue !== $card->getRank()) {
                $hasValueChanged = true;
            }

            $lastType = $card->getSuit();
            $lastValue = $card->getRank();
        }

        if (!$hasTypeChanged) {
            $this->fail("Cards were not randomly created, they all have the same types");
        }

        if (!$hasValueChanged) {
            $this->fail("Cards were not randomly created, they all have the same values");
        }
    }
}
