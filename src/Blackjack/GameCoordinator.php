<?php

namespace Blackjack;

use Blackjack\Event\GameEvent;
use Blackjack\Event\PlayerEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GameCoordinator
{
    /**
     * @var Deck
     */
    private $deck;

    /**
     * @var DeckBuilder
     */
    private $deckBuilder;

    /**
     * @var Game
     */
    private $game;

    /**
     * @var Dealer
     */
    private $dealer;

    /**
     * @var Player
     */
    private $player;

    /**
     * @var HandCalculator
     */
    private $handCalculator;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var WinnerDeterminer
     */
    private $winnerDeterminer;

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);
    }

    public function dealerTurn()
    {
        if (!$this->player->hasBlackjack() && !$this->player->hasBusted()) {
            $this->dispatcher->dispatch(PlayerEvent::DEALER_START_OF_TURN, new PlayerEvent($this->dealer));
            $this->dealer->play($this->handCalculator);
        }

        $this->dispatcher->dispatch(PlayerEvent::DEALER_END_OF_TURN, new PlayerEvent($this->dealer));
    }

    public function playerHit()
    {
        $this->dealer->hit($this->player);
        $this->player->calculateHand($this->handCalculator);

        $this->dispatcher->dispatch(PlayerEvent::PLAYER_HIT, new PlayerEvent($this->player));
    }

    public function playerStand()
    {
        $this->dispatcher->dispatch(PlayerEvent::STAND, new PlayerEvent($this->player));
    }

    public function prepareGame()
    {
        if (!$this->deckBuilder) {
            $this->deckBuilder = new DeckBuilder();
        }

        if (!$this->deck) {
            $this->deck = $this->deckBuilder
                ->startOver()
                ->addAllCards()
                ->shuffle()
                ->getDeck();
        }

        if (!$this->dealer) {
            $this->dealer = new Dealer($this->deck);
        }

        if (!$this->player) {
            $this->player = new Player();
        }

        if (!$this->handCalculator) {
            $this->handCalculator = new HandCalculator();
        }

        if (!$this->dispatcher) {
            $this->dispatcher = new EventDispatcher();
        }

        if (!$this->game) {
            $this->game = new Game();
        }

        $this->game->setPlayer($this->player);
        $this->game->setDealer($this->dealer);

        $this->distributeInitialCards();

        $this->calculateDealerHand();
        $this->calculatePlayerHand();
    }

    private function distributeInitialCards()
    {
        $this->dealer->drawMany(2);
        $this->dealer->hit($this->player, 2);
    }

    private function calculateDealerHand()
    {
        $this->dealer->calculateHand($this->handCalculator);
    }

    private function calculatePlayerHand()
    {
        $this->player->calculateHand($this->handCalculator);
    }

    public function setDealer(Dealer $dealer)
    {
        $this->dealer = $dealer;
    }

    public function setDeckBuilder(DeckBuilder $builder)
    {
        $this->deckBuilder = $builder;
    }

    public function setEventDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function setHandCalculator(HandCalculator $calculator)
    {
        $this->handCalculator = $calculator;
    }

    public function setPlayer(Player $player)
    {
        $this->player = $player;
    }
    
    public function setDeck(Deck $deck)
    {
        $this->deck = $deck;
    }

    public function startGame()
    {
        $this->dispatcher->dispatch(GameEvent::GAME_STARTED, new GameEvent($this->game));

        if ($this->dealer->hasBlackjack()) {
            $this->dispatcher->dispatch(PlayerEvent::DEALER_BLACKJACK, new PlayerEvent($this->dealer));
            $this->endOfGame();
        } elseif ($this->player->hasBlackjack()) {
            $this->dispatcher->dispatch(PlayerEvent::PLAYER_BLACKJACK, new PlayerEvent($this->player));
            $this->playerEndOfTurn();
            $this->endOfGame();
        } else {
            $this->playerTurn();
        }
    }

    private function determineWinner()
    {
        $this->getWinnerDeterminer()->determine($this->game);
    }

    public function playerEndOfTurn()
    {
        $this->dispatcher->dispatch(PlayerEvent::PLAYER_END_OF_TURN, new PlayerEvent($this->player));
    }

    public function endOfGame()
    {
        $this->determineWinner();
        $this->dispatcher->dispatch(GameEvent::GAME_ENDED, new GameEvent($this->getGame()));
    }

    public function playerTurn()
    {
        $this->dispatcher->dispatch(PlayerEvent::PLAYER_START_OF_TURN, new PlayerEvent($this->player));
    }

    public function getWinnerDeterminer(): WinnerDeterminer
    {
        if (null === $this->winnerDeterminer) {
            $this->winnerDeterminer = new WinnerDeterminer();
        }

        return $this->winnerDeterminer;
    }

    public function setWinnerDeterminer(WinnerDeterminer $determiner)
    {
        $this->winnerDeterminer = $determiner;
    }

    /**
     * @return Game|null
     */
    public function getGame()
    {
        return $this->game;
    }

    public function setGame(Game $game)
    {
        $this->game = $game;
    }

    public function getDeck(): Deck
    {
        return $this->deck;
    }

    public function resetGame(Game $game = null)
    {
        $this->deck = $this->player = $this->dealer = null;

        if (!$game) {
            $game = new Game();
        }

        $this->game = $game;
        $this->prepareGame();
    }
}
