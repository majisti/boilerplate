<?php

namespace Unit\Blackjack;

use Blackjack\Card;
use Blackjack\Dealer;
use Blackjack\Deck;
use Blackjack\DeckBuilder;
use Blackjack\Event\GameEvent;
use Blackjack\Event\PlayerEvent;
use Blackjack\Game;
use Blackjack\GameCoordinator;
use Blackjack\HandCalculator;
use Blackjack\Player;
use Blackjack\WinnerDeterminer;
use Mockery as m;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tests\Unit\UnitTest;

/**
 * @method GameCoordinator uut()
 *
 * @property m\MockInterface|EventDispatcher dispatcher
 * @property HandCalculator|m\MockInterface handCalculator
 * @property Player|m\MockInterface player
 * @property Dealer|m\MockInterface dealer
 * @property Game|m\MockInterface game
 * @property m\MockInterface|WinnerDeterminer winnerDeterminer
 * @property DeckBuilder|m\MockInterface deckBuilder
 */
class GameCoordinatorTest extends UnitTest
{
    protected $uut;

    protected function setUp()
    {
        $this->deckBuilder = m::mock(DeckBuilder::class)->shouldDeferMissing();
        $this->dispatcher = m::spy(EventDispatcher::class);
        $this->handCalculator = m::spy(HandCalculator::class);
        $this->player = m::spy(Player::class);
        $this->dealer = m::spy(Dealer::class);
        $this->game = m::spy(Game::class);
        $this->winnerDeterminer = m::spy(WinnerDeterminer::class);

        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        $manager = new GameCoordinator();
        $manager->setEventDispatcher($this->dispatcher);
        $manager->setGame($this->game);
        $manager->setPlayer($this->player);
        $manager->setDealer($this->dealer);
        $manager->setHandCalculator($this->handCalculator);
        $manager->setWinnerDeterminer($this->winnerDeterminer);
        $manager->setDeckBuilder($this->deckBuilder);

        return $manager;
    }

    public function testPrepareGame()
    {
        $this->deckBuilder->shouldReceive('addAllCards')->once()->andReturnSelf();
        $this->deckBuilder->shouldReceive('shuffle')->once()->andReturnSelf();
        $this->deckBuilder->shouldReceive('getDeck')->once()->andReturn(new Deck());

        $this->game->shouldReceive('setPlayer')->once();
        $this->game->shouldReceive('setDealer')->once();

        $this->uut()->prepareGame();
    }

    public function testCanTrickDeck()
    {
        $deck = m::spy(Deck::class);
        $deck->shouldReceive('draw')->andReturn(new Card());

        $this->uut()->setDeck($deck);
        $this->uut()->prepareGame();
    }

    public function testCalculatePlayersHandsAfterCardDistribution()
    {
        $this->dealer->shouldReceive('drawMany')->once()->ordered();
        $this->dealer->shouldReceive('hit')->once()->ordered();

        $this->dealer->shouldReceive('calculateHand')->once()->with($this->handCalculator)->ordered();
        $this->player->shouldReceive('calculateHand')->once()->with($this->handCalculator)->ordered();

        $this->uut()->prepareGame();
    }

    public function testCanAddEventSubscribers()
    {
        $subscriber = m::mock(EventSubscriberInterface::class);

        $this->dispatcher->shouldReceive('addSubscriber')->once()
            ->with($subscriber);

        $this->uut()->addSubscriber($subscriber);
    }

    public function testDealerBlackjackAtStartOfGameSendsAnEvent()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(PlayerEvent::DEALER_BLACKJACK, PlayerEvent::class);

        $this->dealer->shouldReceive('hasBlackjack')->andReturn(false, true);

        $this->uut()->startGame();
        $this->uut()->startGame();
    }

    public function testPlayerEndOfTurnWillSendEvent()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(PlayerEvent::PLAYER_END_OF_TURN, PlayerEvent::class);

        $this->uut()->playerEndOfTurn();
    }

    public function testDealerTurnSendsAnEventAfterHeOutplaysPlayer()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(PlayerEvent::DEALER_END_OF_TURN, PlayerEvent::class);

        $this->uut()->dealerTurn();
    }

    public function testEndOfGameWillDetermineWinner()
    {
        $this->winnerDeterminer->shouldReceive('determine')
            ->once()
            ->with($this->game);

        $this->uut()->endOfGame();
    }

    public function testDealerEndOfTurnWillSendAnEvent()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(PlayerEvent::DEALER_END_OF_TURN, PlayerEvent::class)
            ->ordered()
        ;

        $this->uut()->dealerTurn();
    }

    public function testEndOfGameSendsAnEvent()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(GameEvent::GAME_ENDED, GameEvent::class)
            ->ordered()
        ;

        $this->uut()->endOfGame();
    }

    public function testDealerNeverOutplaysAPlayerThatHasBusted()
    {
        $this->player->shouldReceive('hasBusted')->andReturn(true);
        $this->dealer->shouldReceive('play')->never();
        $this->dispatcher->shouldReceive('dispatch')
            ->with(PlayerEvent::DEALER_END_OF_TURN, PlayerEvent::class)
            ->once();

        $this->uut()->dealerTurn();
    }

    public function testDealerNeverOutplaysAPlayerWithBlackjack()
    {
        $this->player->shouldReceive('hasBlackjack')->andReturn(true);
        $this->dealer->shouldReceive('play')->never();

        $this->uut()->dealerTurn();
    }

    public function testDealerStartOfTurnSendsAnEvent()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(PlayerEvent::DEALER_START_OF_TURN, PlayerEvent::class);

        $this->uut()->dealerTurn();
    }

    public function testDealerTurnTriesToOutplayPlayer()
    {
        $this->dealer->shouldReceive('play')->once();
        $this->uut()->dealerTurn();
    }

    public function testDistributeInitialCardsToParties()
    {
        $this->dealer->shouldReceive('drawMany')->once()->with(2);
        $this->dealer->shouldReceive('hit')->once()->with(Player::class, 2);
        $this->uut()->prepareGame();
    }

    public function testItEndsPlayersTurnOnPlayerBlackjackBySendingEvent()
    {
        $this->player->shouldReceive('hasBlackjack')->andReturn(true);

        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(PlayerEvent::PLAYER_END_OF_TURN, PlayerEvent::class);

        $this->uut()->startGame();
    }

    public function testPlayerBlackjackAtStartOfGameSendsAnEvent()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(PlayerEvent::PLAYER_BLACKJACK, PlayerEvent::class);

        $this->player->shouldReceive('hasBlackjack')->andReturn(false, true);

        $this->uut()->startGame();
        $this->uut()->startGame();
    }

    public function testPlayerBlackjackIsEndOfGame()
    {
        $this->player->shouldReceive('hasBlackjack')->andReturn(true);
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(GameEvent::GAME_ENDED, GameEvent::class);

        $this->uut()->startGame();
    }

    public function testPlayerCanHitDealerAndThenCalculateItsHand()
    {
        $this->dealer->shouldReceive('hit')->once()->with($this->player);
        $this->player->shouldReceive('calculateHand')->once()
            ->with($this->handCalculator);

        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(PlayerEvent::PLAYER_HIT, PlayerEvent::class);

        $this->uut()->playerHit();
    }

    public function testPlayerStandSendsAnEvent()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(PlayerEvent::STAND, PlayerEvent::class);

        $this->uut()->playerStand();
    }

    public function testStartingTheGameWillAlsoStartThePlayersTurn()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(PlayerEvent::PLAYER_START_OF_TURN, PlayerEvent::class);

        $this->uut()->startGame();
    }

    public function testPlayerStartOfTurnSendsAnEvent()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(PlayerEvent::PLAYER_START_OF_TURN, PlayerEvent::class);

        $this->uut()->playerTurn();
    }

    public function testPreparesGame()
    {
        $manager = new GameCoordinator();
        $this->verifyThat($manager->getGame(), is(nullValue()));

        $manager->prepareGame();
        $game = $manager->getGame();

        $this->verifyThat($game->getDealer(), is(notNullValue()));
        $this->verifyThat($game->getPlayer(), is(notNullValue()));
    }

    public function testStartingGameSendsAnEvent()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(GameEvent::GAME_STARTED, GameEvent::class);

        $this->uut()->startGame();
    }

    public function testTriesToDetermineWinnerOnDealerBlackjack()
    {
        $this->dealer->shouldReceive('hasBlackjack')->andReturn(true);

        $this->winnerDeterminer->shouldReceive('determine')->once()
            ->with($this->game);

        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(GameEvent::GAME_ENDED, GameEvent::class)
            ->ordered()
        ;

        $this->uut()->startGame();
    }

    public function testCanResetGame()
    {
        $this->uut()->prepareGame();
        $lastGame = $this->uut()->getGame();
        $lastDeck = $this->uut()->getDeck();

        $this->deckBuilder->shouldReceive('startOver')->once()->andReturnSelf();
        $this->deckBuilder->shouldReceive('shuffle')->andReturnSelf();

        $this->uut()->resetGame();

        $this->verifyThat($this->uut()->getDeck(), is(not(sameInstance($lastDeck))));
        $this->verifyThat($this->uut()->getGame(), not(sameInstance($lastGame)));
    }

    public function testCanResetGameButKeepPlayers()
    {
        $this->player->shouldReceive('resetHand')->once();
        $this->dealer->shouldReceive('resetHand')->once();

        $this->uut()->prepareGame();
        $this->uut()->resetGame(true);

        $this->verifyThat($this->uut()->getPlayer(), is(sameInstance($this->player)));
        $this->verifyThat($this->uut()->getDealer(), is(sameInstance($this->dealer)));
    }
}
