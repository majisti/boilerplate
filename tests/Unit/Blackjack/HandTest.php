<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\Hand;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Hand uut()
 */
class HandTest extends UnitTest
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new Hand();
    }

    public function testIsArrayCollection()
    {
        $this->verifyThat($this->uut(), is(anInstanceOf(ArrayCollection::class)));
    }

    public function testCanTellWhenItContainsAnAce()
    {
        $this->verifyThat($this->uut()->hasAce(), is(false));
        
        $this->uut()->add(new Card(Card::SUIT_DIAMONDS, Card::RANK_ACE));
        $this->uut()->add(new Card(Card::SUIT_DIAMONDS, 1));

        $this->verifyThat($this->uut()->hasAce(), is(true));
    }

    public function testScoresShouldBeZeroWhenNeverSet()
    {
        $this->verifyThat($this->uut()->getBestScore(), equalTo(0));
        $this->verifyThat($this->uut()->getAlternativeScore(), equalTo(0));
        $this->verifyThat($this->uut()->hasAlternateScore(), is(false));
    }

    public function testDeterminesItsBestScore()
    {
        $this->uut()->setScore(10);
        $this->uut()->setAlternateScore(12);

        $this->verifyThat($this->uut()->getBestScore(), equalTo(12));
    }

    public function testGivesItsAlternateScore()
    {
        $this->uut()->setScore(9);
        $this->uut()->setAlternateScore(11);

        $this->verifyThat($this->uut()->getAlternativeScore(), equalTo(9));
    }
    
    public function testCanTellIfHandIsABlackJack()
    {
        $this->verifyThat($this->uut()->hasBlackjack(), is(false));

        $hand = new Hand();
        $hand->add(new Card(Card::SUIT_DIAMONDS, 3));
        $hand->add(new Card(Card::SUIT_DIAMONDS, Card::RANK_ACE));
        $this->verifyThat($hand->hasBlackjack(), is(false));

        $hand = new Hand();
        $hand->add(new Card(Card::SUIT_DIAMONDS, Card::RANK_KING));
        $hand->add(new Card(Card::SUIT_DIAMONDS, Card::RANK_ACE));
        $this->verifyThat($hand->hasBlackjack(), is(true));
        
        $this->uut()->add(new Card(Card::SUIT_DIAMONDS, 1));
        $this->verifyThat($this->uut()->hasBlackjack(), is(false));
    }
}
