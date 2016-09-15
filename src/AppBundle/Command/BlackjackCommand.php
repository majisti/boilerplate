<?php

namespace AppBundle\Command;

use Blackjack\BlackjackPlayer;
use Blackjack\Dealer;
use Blackjack\DeckBuilder;
use Blackjack\Event\PlayerTurnEvent;
use Blackjack\Game;
use Blackjack\HandCalculator;
use Blackjack\Player;
use Blackjack\Ui\AsciiCardDrawer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * WIP. This command is an experiment only and will be refactored with proper
 * TDD/BDD.
 */
class BlackjackCommand extends Command implements EventSubscriberInterface
{
    private $dispatcher;

    /**
     * @var AsciiCardDrawer
     */
    private $drawer;

    /**
     * @var Player
     */
    private $player;

    /**
     * @var Dealer
     */
    private $dealer;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var HandCalculator
     */
    private $handCalculator;

    protected function configure()
    {
        $this
            ->setName('majisti:game:blackjack')
            ->setDescription('Play a game of BlackJack against the computer!')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //this is far from complete yet, just playing around with ascii card drawing

        $this->input = $input;
        $this->output = $output;

        $deck = (new DeckBuilder())
            ->addAllCards()
            ->shuffle()
            ->getDeck();

        $this->dealer = new Dealer($deck);
        $this->player = new Player();
        $this->handCalculator = new HandCalculator();
        $this->drawer = new AsciiCardDrawer();

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber($this);

        $game = new Game();
        $game->setDealer($this->dealer);
        $game->setPlayer($this->player);
        $game->setEventDispatcher($this->dispatcher);

        $game->initialize();

        $this->calculateDealerHand();
        $this->calculatePlayerHand();

        if ($this->dealer->hasBlackjack()) {
            $this->drawBoard(false);
            $this->endOfGame();
        } else {
            $this->drawBoard(true);

            $game->start();
            $this->play();
        }
    }

    public function play()
    {
        $this->output->writeln("Player's turn!");

        if ($this->player->hasBlackjack()) {
            $this->output->writeln('<comment>Blackjack!</comment>');
            $this->player->endOfTurn($this->dispatcher);

            return;
        }

        do {
            $question = new ChoiceQuestion('Hit or stand?', ['hit', 'stand']);

            /* @var $helper QuestionHelper */
            $helper = $this->getHelper('question');
            $answer = $helper->ask($this->input, $this->output, $question);

            switch ($answer) {
                case 'hit':
                    $this->player->hit($this->dealer);
                    $this->calculatePlayerHand();
                    $this->drawBoard();
                    break;
                case 'stand':
                    $this->player->stand($this->dispatcher);
            }
        } while (!$this->player->hasBusted() && $answer == 'hit');

        if ($this->player->hasBusted()) {
            $this->output->writeln('<comment>Busted!</comment>');
            $this->player->endOfTurn($this->dispatcher);
        }
    }

    public function playerEndOfTurn(PlayerTurnEvent $event)
    {
        $this->output->writeln("Player's end of turn.");

        if (!$this->player->hasBusted()) {
            $this->drawBoard();
            $this->dealerAutomaticAi();
        }
    }

    protected function drawPlayersHand()
    {
        $this->drawer->setShouldHideFirstCard(false);
        $this->output->writeln('<info>PLAYER #1</info>');
        $this->output->writeln($this->drawer->drawCards($this->player->getCards()));
        $this->drawScore($this->player);
    }

    protected function drawDealerHand(bool $hideFirstCard = false)
    {
        $this->drawer->setShouldHideFirstCard($hideFirstCard);
        $this->output->writeln('<info>DEALER</info>');
        $this->output->writeln($this->drawer->drawCards($this->dealer->getHand()->toArray()));

        if (!$hideFirstCard) {
            $this->drawScore($this->dealer);
        }
    }

    protected function calculateDealerHand()
    {
        $this->handCalculator->calculate($this->dealer->getHand());
    }

    protected function calculatePlayerHand()
    {
        $this->handCalculator->calculate($this->player->getHand());
    }

    protected function drawBoard(bool $hideDealersCard = false)
    {
        $this->drawDealerHand($hideDealersCard);
        $this->drawPlayersHand();
    }

    protected function drawScore(BlackjackPlayer $player)
    {
        if ($player->hasBlackjack()) {
            $this->output->writeln('Blackjack!');
        } else {
            $this->output->write(sprintf('Score: %s', $player->getBestScore()));

            if ($player->hasAlternativeScore()) {
                $this->output->write(sprintf('/%s', $player->getAlternativeScore()));
            }

            $this->output->writeln('');
            $this->output->writeln('');
        }
    }

    private function dealerAutomaticAi()
    {
        if (!$this->player->hasBlackjack()) {
            while ($this->dealer->hasToDraw() && !$this->dealer->hasBusted()) {
                $this->dealer->drawManyCards(1);
                $this->calculateDealerHand();
            }

            while ($this->dealer->getBestScore() <= $this->player->getBestScore() && !$this->dealer->hasBusted()) {
                $this->dealer->drawManyCards(1);
                $this->calculateDealerHand();
            }
        }

        $this->drawBoard();

        if ($this->dealer->hasBusted()) {
            $this->output->writeln('<comment>Dealer busted. Player wins!</comment>');
        } elseif ($this->dealer->getBestScore() === $this->player->getBestScore()) {
            $this->output->writeln('<comment>Draw!</comment>');
        } else {
            $this->output->writeln('<comment>Dealer wins!</comment>');
        }
    }

    private function endOfGame()
    {
        //todo:
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PlayerTurnEvent::END_OF_TURN => 'playerEndOfTurn',
        ];
    }
}
