<?php

namespace Unit\Blackjack;

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

    public function testBuildStandardDeckAndShufflesIt()
    {
        $this->deckBuilder->shouldReceive('addAllCards')->once()->andReturnSelf();
        $this->deckBuilder->shouldReceive('shuffle')->once()->andReturnSelf();
        $this->deckBuilder->shouldReceive('getDeck')->once()->andReturn(new Deck());

        $this->uut()->prepareGame();
    }

    public function testCalculatePlayersHandsAfterGameInitialization()
    {
        $this->game->shouldReceive('initialize')->once()->ordered();
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
        $this->dealer->shouldReceive('outplay')->never();
        $this->dispatcher->shouldReceive('dispatch')
            ->with(PlayerEvent::DEALER_END_OF_TURN, PlayerEvent::class)
            ->never();

        $this->uut()->dealerTurn();
    }

    public function testDealerNeverOutplaysAPlayerWithBlackjack()
    {
        $this->player->shouldReceive('hasBlackjack')->andReturn(true);
        $this->dealer->shouldReceive('outplay')->never();

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
        $this->dealer->shouldReceive('outplay')->once();
        $this->uut()->dealerTurn();
    }

    public function testInitializesGame()
    {
        $this->game->shouldReceive('initialize')->once();
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
}
