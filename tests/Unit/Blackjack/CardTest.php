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
    
//    public function testCanCreateRandomCard()
//    {
//        $this->markTestSkipped("sometimes this test fails due to poor random checks");
//
//        $this->verifyThat($this->uut()->createRandom(), is(anInstanceOf(Card::class)));
//
//        $hasTypeChanged = false;
//        $hasValueChanged = false;
//        $numberOfTries = 0;
//        $lastValue = null;
//        $lastType = null;
//
//        while (!$hasTypeChanged && !$hasValueChanged && ++$numberOfTries < 100) {
//            $card = $this->uut()->createRandom();
//
//            if ($lastType !== null && $lastType !== $card->getSuit()) {
//                $hasTypeChanged = true;
//            }
//
//            if ($lastValue !== null && $lastValue !== $card->getRank()) {
//                $hasValueChanged = true;
//            }
//
//            $lastType = $card->getSuit();
//            $lastValue = $card->getRank();
//        }
//
//        if (!$hasTypeChanged) {
//            $this->fail("Cards were not randomly created, they all have the same types");
//        }
//
//        if (!$hasValueChanged) {
//            $this->fail("Cards were not randomly created, they all have the same values");
//        }
//    }

    public function testCanEstimateScoreValue()
    {
        $card = new Card(Card::SUIT_DIAMONDS, 8);
        $this->verifyThat(Card::estimateScoreValue($card), equalTo(8));

        $card = new Card(Card::SUIT_DIAMONDS, 10);
        $this->verifyThat(Card::estimateScoreValue($card), equalTo(10));

        $card = new Card(Card::SUIT_DIAMONDS, Card::RANK_JACK);
        $this->verifyThat(Card::estimateScoreValue($card), equalTo(10));

        $card = new Card(Card::SUIT_DIAMONDS, Card::RANK_QUEEN);
        $this->verifyThat(Card::estimateScoreValue($card) , equalTo(10));

        $card = new Card(Card::SUIT_DIAMONDS, Card::RANK_KING);
        $this->verifyThat(Card::estimateScoreValue($card) , equalTo(10));
    }
}
