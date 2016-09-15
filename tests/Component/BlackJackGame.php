<?php

namespace Component;

use Blackjack\Dealer;
use Blackjack\DeckBuilder;
use Blackjack\Game;
use Blackjack\Player;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\Component\ComponentTest;

class BlackJackGame extends ComponentTest
{
    public function testGame()
    {
        $this->markTestIncomplete();
        
        $deck = (new DeckBuilder())
            ->addAllCards()
            ->shuffle()
            ->getDeck();

        $dealer = new Dealer($deck);
        $player = new Player();
        
        $dispatcher = new EventDispatcher();
        
        $game = new Game();
        $game->setDealer($dealer);
        $game->setPlayer($player);
        $game->setEventDispatcher($dispatcher);
        
        $game->start();
    }
}