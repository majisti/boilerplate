<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\CardCollection;
use Blackjack\Hand;
use Tests\Unit\UnitTest;

/**
 * @method Hand uut()
 */
class HandTest extends UnitTest
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Hand();
    }

    public function testCanTellIfHandIsABlackJack()
    {
        $this->verifyThat($this->uut()->hasBlackjack(), is(false));

        $hand = new Hand();
        $hand->addCards([new Card(3), new Card(Card::RANK_ACE)]);
        $this->verifyThat($hand->hasBlackjack(), is(false));

        $hand = new Hand();
        $hand->addCards([new Card(Card::RANK_KING), new Card(Card::RANK_ACE)]);
        $this->verifyThat($hand->hasBlackjack(), is(true));

        $this->uut()->addCard(new Card(1));
        $this->verifyThat($this->uut()->hasBlackjack(), is(false));
    }

    public function testCanTellWhenItContainsAnAce()
    {
        $this->verifyThat($this->uut()->hasAce(), is(false));

        $this->uut()->addCards([new Card(Card::RANK_ACE), new Card(1)]);

        $this->verifyThat($this->uut()->hasAce(), is(true));
    }

    public function testDeterminesItsBestScore()
    {
        $this->uut()->setScore(10);
        $this->uut()->setAlternateScore(12);

        $this->verifyThat($this->uut()->getBestScore(), equalTo(12));

        $this->uut()->setScore(12);
        $this->uut()->setAlternateScore(23);

        $this->verifyThat($this->uut()->getBestScore(), equalTo(12));
    }

    public function testGivesItsAlternateScore()
    {
        $this->uut()->setScore(9);
        $this->uut()->setAlternateScore(11);

        $this->verifyThat($this->uut()->getAlternativeScore(), equalTo(9));

        $this->uut()->setScore(18);
        $this->uut()->setAlternateScore(23);

        $this->verifyThat($this->uut()->getAlternativeScore(), equalTo(23));
    }

    public function testHasBusted()
    {
        $this->verifyThat($this->uut()->hasBusted(), is(false));

        $this->uut()->setScore(22);
        $this->uut()->setAlternateScore(23);
        $this->verifyThat($this->uut()->hasBusted(), is(true));

        $this->uut()->setScore(22);
        $this->uut()->setAlternateScore(0);
        $this->verifyThat($this->uut()->hasBusted(), is(true));

        $this->uut()->setScore(22);
        $this->uut()->setAlternateScore(11);
        $this->verifyThat($this->uut()->hasBusted(), is(false));
    }

    public function testIsCardCollection()
    {
        $this->verifyThat($this->uut(), is(anInstanceOf(CardCollection::class)));
    }

    public function testScoresShouldBeZeroWhenNeverSet()
    {
        $this->verifyThat($this->uut()->getBestScore(), equalTo(0));
        $this->verifyThat($this->uut()->getAlternativeScore(), equalTo(0));
        $this->verifyThat($this->uut()->hasAlternateScore(), is(false));
    }
}
