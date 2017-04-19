<?php

namespace Spec\Blackjack;

use Blackjack\Dealer;
use Blackjack\Player;
use Spec\BaseSpec;

class WinnerDeterminerSpec extends BaseSpec
{
    public function it_determines_dealer_as_winner_when_he_has_blackjack(Dealer $dealer, Player $player)
    {
        $dealer->hasBlackjack()->willReturn(true);
        $player->hasBlackjack()->willReturn(false);

        $this->determine($dealer, $player)->getWinner()->shouldBeEqualTo($dealer);
    }

    public function it_determines_player_as_winner_when_he_has_blackjack(Dealer $dealer, Player $player)
    {
        $dealer->hasBlackjack()->willReturn(false);
        $player->hasBlackjack()->willReturn(true);

        $this->determine($dealer, $player)->getWinner()->shouldBeEqualTo($player);
    }

    public function it_determines_draw_when_both_parties_have_blackjack(Dealer $dealer, Player $player)
    {
        $dealer->hasBlackjack()->willReturn(true);
        $player->hasBlackjack()->willReturn(true);

        $this->determine($dealer, $player)->isDraw()->shouldBe(true);
    }

    public function it_determines_dealer_as_winner_on_best_score(Dealer $dealer, Player $player)
    {
        $this->nobodyHasBlackjack($dealer, $player);
        $this->nobodyBusts($dealer, $player);

        $dealer->getBestScore()->willReturn(18);
        $player->getBestScore()->willReturn(15);

        $this->determine($dealer, $player)->getWinner()->shouldBeEqualTo($dealer);
    }

    public function it_determines_player_as_winner_on_best_score(Dealer $dealer, Player $player)
    {
        $this->nobodyHasBlackjack($dealer, $player);
        $this->nobodyBusts($dealer, $player);

        $dealer->getBestScore()->willReturn(15);
        $player->getBestScore()->willReturn(18);

        $this->determine($dealer, $player)->getWinner()->shouldBeEqualTo($player);
    }

    public function it_determines_draw_on_equal_score(Dealer $dealer, Player $player)
    {
        $this->nobodyHasBlackjack($dealer, $player);
        $this->nobodyBusts($dealer, $player);

        $dealer->getBestScore()->willReturn(15);
        $player->getBestScore()->willReturn(15);

        $this->determine($dealer, $player)->isDraw()->shouldBe(true);
    }

    public function it_determines_dealer_as_winner_when_player_busts(Dealer $dealer, Player $player)
    {
        $this->nobodyHasBlackjack($dealer, $player);

        $dealer->hasBusted()->willReturn(false);
        $player->hasBusted()->willReturn(true);

        $this->determine($dealer, $player)->getWinner()->shouldEqual($dealer);
    }

    public function it_determines_player_as_winner_when_dealer_busts(Dealer $dealer, Player $player)
    {
        $this->nobodyHasBlackjack($dealer, $player);

        $dealer->hasBusted()->willReturn(true);
        $player->hasBusted()->willReturn(false);

        $this->determine($dealer, $player)->getWinner()->shouldEqual($player);
    }

    protected function nobodyBusts(Dealer $dealer, Player $player)
    {
        $dealer->hasBusted()->willReturn(false);
        $player->hasBusted()->willReturn(false);
    }

    /**
     * @param Dealer $dealer
     * @param Player $player
     */
    protected function nobodyHasBlackjack(Dealer $dealer, Player $player)
    {
        $dealer->hasBlackjack()->willReturn(false);
        $player->hasBlackjack()->willReturn(false);
    }
}
