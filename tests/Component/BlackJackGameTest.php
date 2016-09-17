<?php

namespace Component;

use Blackjack\Card;
use Blackjack\Dealer;
use Blackjack\Deck;
use Blackjack\DeckBuilder;
use Blackjack\Event\PlayerEvent;
use Blackjack\Game;
use Blackjack\GameCoordinator;
use Mockery as m;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\Component\ComponentTest;

/**
 * @property  dealer
 * @property Dealer|m\MockInterface dealer
 * @property m\MockInterface|EventDispatcher dispatcher
 * @property DeckBuilder|m\MockInterface deckBuilder
 * @property Deck deck
 * @property GameCoordinator gameCoordinator
 */
class BlackJackGameTest extends ComponentTest
{
    protected function setUp()
    {
        $this->deck = new Deck();

        $this->dispatcher = m::spy(new EventDispatcher());
        $this->gameCoordinator = new GameCoordinator();
        $this->gameCoordinator->setDeck($this->deck);
        $this->gameCoordinator->setEventDispatcher($this->dispatcher);
    }

    private function initialDealerCards(Card $firstCard, Card $secondCard)
    {
        $this->prependCardsToDeck([$firstCard, $secondCard]);
    }

    /**
     * @param Card[] $cards
     */
    private function prependCardsToDeck(array $cards)
    {
        $this->deck->prependCards($cards);
    }

    private function initialPlayerCards(Card $firstCard, Card $secondCard)
    {
        $this->prependCardsToDeck([$firstCard, $secondCard]);
    }

    /**
     * @param Card[] $cards
     */
    private function nextPlayerCards(array $cards)
    {
        $this->prependCardsToDeck($cards);
    }

    /**
     * @param Card[] $cards
     */
    private function nextDealerCards(array $cards)
    {
        $this->prependCardsToDeck($cards);
    }

    protected function nextCardsFormABlackjack()
    {
        $this->prependCardsToDeck([new Card(Card::RANK_QUEEN), new Card(Card::RANK_ACE)]);
    }

    protected function playerWillHitTimes(int $count = 1)
    {
        $this->dispatcher->addListener(PlayerEvent::PLAYER_START_OF_TURN, function () use ($count) {
            for ($i = $count; $i <= $count; ++$i) {
                $this->gameCoordinator->playerHit();
            }
        });
    }

    protected function playEntireGame(): Game
    {
        $game = $this->coordinateGame();
        $this->gameCoordinator->playerEndOfTurn();
        $this->gameCoordinator->dealerTurn();
        $this->gameCoordinator->endOfGame();

        return $game;
    }

    protected function coordinateGame(): Game
    {
        $this->gameCoordinator->prepareGame();
        $this->gameCoordinator->startGame();

        return $this->gameCoordinator->getGame();
    }

    public function testDealerLosesIfHeBusts()
    {
        $this->initialDealerCards(new Card(10), new Card(3));
        $this->initialPlayerCards(new Card(10), new Card(8));
        $this->nextPlayerCards([new Card(3)]);
        $this->nextDealerCards([new Card(9)]);

        $this->playerWillHitTimes(1);
        $game = $this->playEntireGame();

        $this->verifyThat($game->getDealer()->hasBusted(), is(true));
        $this->verifyThat($game->hasPlayerWon(), is(true));
    }

    public function testDealerShouldTryToStopAt17()
    {
        $this->initialDealerCards(new Card(10), new Card(3));
        $this->initialPlayerCards(new Card(10), new Card(8));
        $this->nextDealerCards([new Card(4), new Card(10)]);

        $game = $this->playEntireGame();

        $this->verifyThat($game->getDealer()->getBestScore(), is(equalTo(17)));
        $this->verifyThat($game->hasPlayerWon(), is(true));
    }

    public function testDealerWinsWithBlackjackAtStartOfGame()
    {
        $this->nextCardsFormABlackjack();
        $this->initialPlayerCards(new Card(2), new Card(3));

        $game = $this->coordinateGame();
        $this->verifyThat($game->hasDealerWon(), is(true));
    }

    public function testGameDrawWhenBothPlayersHaveABlackjack()
    {
        $this->nextCardsFormABlackjack();
        $this->nextCardsFormABlackjack();

        $game = $this->coordinateGame();
        $this->verifyThat($game->isDraw(), is(true));
    }

    public function testGameDrawWhenBothPlayersHaveTheSameScore()
    {
        $this->initialDealerCards(new Card(10), new Card(3));
        $this->initialPlayerCards(new Card(10), new Card(8));
        $this->nextDealerCards([new Card(5)]);

        $game = $this->playEntireGame();
        $this->verifyThat($game->isDraw(), is(true));
    }

    public function testPlayerLosesIfHeBusts()
    {
        $this->initialPlayerCards(new Card(10), new Card(3));
        $this->initialPlayerCards(new Card(10), new Card(8));
        $this->nextPlayerCards([new Card(5)]);

        $this->playerWillHitTimes(1);
        $game = $this->playEntireGame();

        $this->verifyThat($game->getPlayer()->hasBusted(), is(true));
        $this->verifyThat($game->hasDealerWon(), is(true));
    }

    public function testPlayerWillUseBestScoreInOrderToWin()
    {
        $this->initialDealerCards(new Card(10), new Card(8));
        $this->initialDealerCards(new Card(5), new Card(Card::RANK_ACE));
        $this->nextDealerCards([new Card(3)]);

        $this->playerWillHitTimes(1);
        $game = $this->playEntireGame();

        $this->verifyThat($game->hasPlayerWon(), is(true));
    }

    public function testPlayerWinsWithBlackjackAtStartOfGame()
    {
        $this->initialDealerCards(new Card(10), new Card(9));
        $this->nextCardsFormABlackjack();

        $game = $this->coordinateGame();
        $this->verifyThat($game->hasPlayerWon(), is(true));
    }
}
