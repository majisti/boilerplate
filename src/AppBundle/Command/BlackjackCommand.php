<?php

namespace AppBundle\Command;

use Blackjack\Card;
use Blackjack\Dealer;
use Blackjack\Deck;
use Blackjack\Event\GameEvent;
use Blackjack\Event\PlayerEvent;
use Blackjack\Game;
use Blackjack\GameCoordinator;
use Blackjack\Hand;
use Blackjack\Player;
use Blackjack\Ui\AsciiCardDrawer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlackjackCommand extends Command implements EventSubscriberInterface
{
    const OPTION_CHEAT = 'cheat';

    /**
     * @var GameCoordinator
     */
    private $gameCoordinator;

    /**
     * @var AsciiCardDrawer
     */
    private $cardDrawer;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    private $hideHoleCard = true;

    public function __construct(GameCoordinator $gameCoordinator)
    {
        $this->gameCoordinator = $gameCoordinator;
        parent::__construct();
    }

    protected function configure()
    {

        $cheatDescription = <<<EOF
Tricks the deck by passing in predefined cards. 
This wipes the deck. Ex: <info>--cheat=10,3,5,1</info>. 
The cards are added in FILO order. The dealer's card will also be revealed.
<comment>Provide enough cards so that you or the dealer can play!</comment>
EOF;

        $this
            ->setName('majisti:game:blackjack')
            ->setDescription('Play a game of BlackJack against the computer!')
            ->addOption(self::OPTION_CHEAT, 'c', InputOption::VALUE_OPTIONAL, $this->getDescriptionOneOneLine($cheatDescription))
        ;
    }

    public function dealerBlackjack(PlayerEvent $event)
    {
        $this->hideHoleCard = false;
        $this->drawBoard($this->gameCoordinator->getGame());
    }

    public function dealerEndOfTurn(PlayerEvent $event)
    {
        $dealer = $event->getPlayer();

        $game = $this->gameCoordinator->getGame();
        if (!$game->getPlayer()->hasBusted()) {
            $this->drawBoard($game);
        }

        if ($dealer->hasBusted()) {
            $this->output->writeln('<comment>Dealer busted!</comment>');
        }

        $this->gameCoordinator->endOfGame();
    }

    protected function drawBoard(Game $game)
    {
        $this->drawDealerHand($game->getDealer());
        $this->drawPlayerHand($game->getPlayer());
    }

    protected function drawDealerHand(Dealer $dealer)
    {
        $this->cardDrawer->setShouldHideFirstCard($this->hideHoleCard);
        $this->output->writeln('<info>DEALER</info>');
        $this->output->writeln($this->cardDrawer->drawCards($dealer->getHand()->toArray()));

        if (!$this->hideHoleCard) {
            $this->drawScore($dealer);
        }
    }

    protected function drawPlayerHand(Player $player)
    {
        $this->cardDrawer->setShouldHideFirstCard(false);
        $this->output->writeln('<info>PLAYER #1</info>');
        $this->output->writeln($this->cardDrawer->drawCards($player->getCards()));
        $this->drawScore($player);
    }

    protected function drawScore(Player $player)
    {
        if ($player->hasBlackjack()) {
            $this->output->writeln('Blackjack!');
        } else {
            $this->output->write(sprintf('Score: <info>%s</info>', $player->getBestScore()));

            if ($player->hasAlternativeScore()) {
                $this->output->write(sprintf('/%s', $player->getAlternativeScore()));
            }

            $this->output->writeln('');
            $this->output->writeln('');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->cardDrawer = new AsciiCardDrawer();

        if ($cardsList = $input->getOption(self::OPTION_CHEAT)) {
            $this->cheat($cardsList);
        }

        $this->gameCoordinator->prepareGame();
        $this->gameCoordinator->addSubscriber($this);
        $this->gameCoordinator->startGame();
    }

    public function gameEnd(GameEvent $event)
    {
        $game = $event->getGame();

        if ($game->isDraw()) {
            $this->output->writeln('<comment>Draw!</comment>');
        } elseif ($game->hasDealerWon()) {
            $this->output->writeln('<comment>Dealer wins!</comment>');
        } elseif ($game->hasPlayerWon()) {
            $this->output->writeln('<comment>You win!</comment>');
        } else {
            $this->output->writeln('<comment>You lose!</comment>');
        }

        $this->askToPlayAgain();
    }

    public function gameStart(GameEvent $event)
    {
        $this->drawBoard($event->getGame());
    }

    public function playerEndOfTurn(PlayerEvent $event)
    {
        $player = $event->getPlayer();

        if (!$player->hasBusted()) {
            $this->drawBoard($this->gameCoordinator->getGame());
        }

        $this->hideHoleCard = false;
    }

    public function playerTurn(PlayerEvent $event)
    {
        $player = $event->getPlayer();

        $this->output->writeln("Player's turn!");

        do {
            $question = new ChoiceQuestion('Hit or stand?', ['hit', 'stand']);

            /* @var $helper QuestionHelper */
            $helper = $this->getHelper('question');
            $answer = $helper->ask($this->input, $this->output, $question);

            switch ($answer) {
                case 'hit':
                    $this->gameCoordinator->playerHit();
                    $this->drawBoard($this->gameCoordinator->getGame());
                    break;
                case 'stand':
                    break;
            }
        } while (!$player->hasBusted() && $answer === 'hit' && $player->getBestScore() < Hand::MAXIMUM_SCORE);

        if ($player->hasBusted()) {
            $this->output->writeln('<comment>You busted!</comment>');
        }

        $this->gameCoordinator->playerEndOfTurn();
        $this->gameCoordinator->dealerTurn();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            GameEvent::GAME_STARTED => 'gameStart',
            GameEvent::GAME_ENDED => 'gameEnd',
            PlayerEvent::DEALER_BLACKJACK => 'dealerBlackjack',
            PlayerEvent::PLAYER_START_OF_TURN => 'playerTurn',
            PlayerEvent::PLAYER_END_OF_TURN => 'playerEndOfTurn',
            PlayerEvent::DEALER_END_OF_TURN => 'dealerEndOfTurn',
        ];
    }

    private function askToPlayAgain()
    {
        $question = new ConfirmationQuestion('Do you want to play again? [y/n] ');

        /* @var $helper QuestionHelper */
        $helper = $this->getHelper('question');
        $answer = $helper->ask($this->input, $this->output, $question);

        switch ($answer) {
            case 'y':
                $this->resetGame();
                break;
            case false:
                $this->output->writeln('Thank you for playing!');
                break;
        }
    }

    protected function resetGame()
    {
        $this->hideHoleCard = true;
        $this->gameCoordinator->resetGame();
        $this->gameCoordinator->startGame();
    }

    protected function cheat(string $cardsList)
    {
        $this->hideHoleCard = false;

        $cards = explode(',', $cardsList);

        $deck = new Deck();
        foreach ($cards as $card) {
            $deck->addCard(new Card((int)$card));
        }

        $this->gameCoordinator->setDeck($deck);
    }

    /**
     * @param $cheatDescription
     *
     * @return string
     */
    protected function getDescriptionOneOneLine($cheatDescription)
    {
        return trim(preg_replace('/\s+/', ' ', $cheatDescription));
    }
}
