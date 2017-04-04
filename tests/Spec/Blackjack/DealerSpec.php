<?php

namespace Spec\Blackjack;

use Blackjack\Card;
use Blackjack\Dealer;
use Blackjack\Deck;
use Blackjack\Hand;
use Blackjack\HandCalculator;
use Blackjack\Player;
use Prophecy\Argument;
use Spec\BaseSpec;

class DealerSpec extends BaseSpec
{
    function let(Deck $deck)
    {
        $deck->draw()->willReturn(new Card());
        $this->beConstructedWith($deck);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Dealer::class);
    }

    function it_can_give_cards_to_himself()
    {
        $hand = $this->drawMany(2);

        $hand->shouldBeAnInstanceOf(Hand::class);
        $hand->count()->shouldBeEqualTo(2);
    }

    function it_can_give_cards_to_player(Player $player)
    {
        $player->receiveCard(Argument::type(Card::class))->shouldBeCalledTimes(2);
        $this->hit($player, 2);
    }

    function it_cannot_draw_if_it_has_more_than_sixteen(Deck $deck, Player $player)
    {
        $handCalculator = new HandCalculator();

        $deck->draw()->willReturn(new Card(1));
        $this->beConstructedWith($deck);

        $this->receiveCards([new Card(10), new Card(6)]);
        $this->calculateHand($handCalculator);

        $player->getBestScore()->willReturn(18);

        $this->play($handCalculator);
        $this->getBestScore()->shouldEqual(17);
    }

    function it_tries_to_beat_player(Deck $deck, Player $player)
    {
        $this->beConstructedWith($deck);

        $deck->draw()->willReturn(new Card(2));

        $player->getBestScore()->willReturn(12);
        $this->play(new HandCalculator());

        $this->getBestScore()->shouldEqual(18);
    }

    function it_knows_if_it_must_continue_to_draw(Hand $hand)
    {
        $hand->getBestScore()->willReturn(16, 17);
        $this->setHand($hand);

        $this->hasToDraw()->shouldBe(true);
        $this->hasToDraw()->shouldBe(false);
    }
}
