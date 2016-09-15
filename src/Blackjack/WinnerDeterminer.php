<?php

namespace Blackjack;

class WinnerDeterminer
{
    public function determine(Game $game)
    {
        $dealer = $game->getDealer();
        $player = $game->getPlayer();

        if ($dealer->hasBlackjack() && $player->hasBlackjack()) {
            $game->setIsDraw();
        } elseif ($dealer->hasBlackjack()) {
            $game->dealerWins();
        } elseif ($player->hasBlackjack()) {
            $game->playerWins();
        } elseif ($player->hasBusted() || $dealer->getBestScore() > $player->getBestScore()
            && !$dealer->hasBusted()) {
            $game->dealerWins();
        } elseif ($dealer->getBestScore() !== $player->getBestScore()) {
            $game->playerWins();
        } else {
            $game->setIsDraw();
        }
    }
}
