<?php

namespace Blackjack;

class WinnerDeterminer
{
    public function determine(Dealer $dealer, Player $player)
    {
        $game = new Game();
        $game->setPlayer($player);
        $game->setDealer($dealer);

        if ($dealer->hasBlackjack() && $player->hasBlackjack()) {
            $game->setIsDraw();
        } elseif ($dealer->hasBlackjack()) {
            $game->dealerWins();
        } elseif ($player->hasBlackjack()) {
            $game->playerWins();
        } elseif ($player->hasBusted()) {
            $game->dealerWins();
        } elseif ($dealer->hasBusted()) {
            $game->playerWins();
        } elseif ($dealer->getBestScore() > $player->getBestScore()) {
            $game->dealerWins();
        } elseif ($dealer->getBestScore() !== $player->getBestScore()) {
            $game->playerWins();
        } else {
            $game->setIsDraw();
        }

        return $game;
    }
}
