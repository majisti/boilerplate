<?php

namespace Unit\Blackjack;

use AspectMock\Test;
use Blackjack\Card;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Card uut()
 */
class CardTest extends UnitTest
{
    protected $uut;

    protected function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Card();
    }
    
    public function testCanCreateRandomCard()
    {
        $phpRandFunction = Test::func($this->getUutNamespace(), 'rand', Card::RANK_ACE);

        $card = $this->uut()->createRandom();
        $this->verifyThat($card, is(anInstanceOf(Card::class)));
        $this->verifyThat($card->getRank(), is(equalTo(Card::RANK_ACE)));

        $phpRandFunction->verifyInvoked([Card::RANK_ACE, Card::RANK_KING]);
    }

    public function testCanEstimateScoreValue()
    {
        $card = new Card(8);
        $this->verifyThat(Card::estimateScoreValue($card), equalTo(8));

        $card = new Card(10);
        $this->verifyThat(Card::estimateScoreValue($card), equalTo(10));

        $card = new Card(Card::RANK_JACK);
        $this->verifyThat(Card::estimateScoreValue($card), equalTo(10));

        $card = new Card(Card::RANK_QUEEN);
        $this->verifyThat(Card::estimateScoreValue($card) , equalTo(10));

        $card = new Card(Card::RANK_KING);
        $this->verifyThat(Card::estimateScoreValue($card) , equalTo(10));
    }
}
