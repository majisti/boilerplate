<?php

namespace Tests\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Blackjack\Card;
use Blackjack\Deck;
use Blackjack\GameCoordinator;
use Mockery as m;
use PSS\Behat\Symfony2MockerExtension\ServiceMocker;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Tests\Utils\Hamcrest;

class BlackjackContext implements Context
{
    use KernelDictionary;
    use Hamcrest;

    const QUESTION_HIT_OR_STAND = 'Hit or stand';
    const QUESTION_PLAY_AGAIN = 'play again';

    /**
     * @var Deck
     */
    private $deck;

    /**
     * @var GameCoordinator|m\MockInterface
     */
    private $gameCoordinator;

    /**
     * @var ServiceMocker
     */
    private $serviceMocker;

    /**
     * @var CommandContext
     */
    private $commandContext;

    public function __construct(ServiceMocker $serviceMocker)
    {
        $this->serviceMocker = $serviceMocker;
    }

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->commandContext = $environment->getContext(CommandContext::class);
    }

    /**
     * @Given /^I trick the deck$/
     */
    public function iTrickTheDeck()
    {
        $this->deck = new Deck();

        $this->gameCoordinator = $this->serviceMocker
            ->mockService('app.games.blackjack.game_coordinator', GameCoordinator::class)
            ->shouldDeferMissing();
        $this->gameCoordinator->setDeck($this->deck);
    }

    /**
     * @Given /^I call "([^"]*)" when asked for my move$/
     */
    public function iAnswerWhenAskedForMyMove(string $moves)
    {
        $moves = explode(', ', $moves);

        $this->commandContext->getQuestionHelper()
            ->shouldReceive('ask')
            ->with(anything(), anything(), anInstanceOf(ChoiceQuestion::class))
            ->andReturnValues($moves);
    }

    /**
     * @Then /^the dealer should have won$/
     */
    public function theDealerShouldHaveWon()
    {
        $this->commandContext->iShouldSeeInTheCommandOutput('Dealer wins!');
    }

    /**
     * @Given /^the player should have won$/
     */
    public function thePlayerShouldHaveWon()
    {
        $this->commandContext->iShouldSeeInTheCommandOutput('You win!');
    }

    /**
     * @Then /^the player score should be "([^"]*)"$/
     */
    public function thePlayerScoreShouldBe(int $score)
    {
        $this->commandContext->iShouldSeeInTheCommandOutput(sprintf('Score: %s', $score));
    }

    /**
     * @Given /^the dealer score should be "([^"]*)"$/
     */
    public function theDealerScoreShouldBe(int $score)
    {
        $game = $this->gameCoordinator->getGame();
        $this->verifyThat($game->getDealerBestScore(), equalTo($score));
    }

    /**
     * @AfterStep
     */
    public function printCurrentBoard(AfterStepScope $score)
    {
        if (!$score->getTestResult()->isPassed()) {
            $this->iPrintCurrentBlackjackGame();
        }
    }

    /**
     * @Then /^I print current blackjack game$/
     */
    public function iPrintCurrentBlackjackGame()
    {
        $display = $this->commandContext->getDisplay();

        if (!empty($display)) {
            echo $display;
        }
    }

    /**
     * @Then /^I will be asked for a rematch and answer "([^"]*)"$/
     */
    public function iWillBeAskedForARematchAndAnswer(string $answersList)
    {
        $answers = explode(',', $answersList);

        $this->commandContext->getQuestionHelper()
            ->shouldReceive('ask')
            ->once()
            ->with(anything(), anything(), anInstanceOf(ConfirmationQuestion::class))
            ->andReturnValues($answers)
        ;
    }

    /**
     * @Given /^I will have a blackjack$/
     */
    public function iWillHaveABlackjack()
    {
        $this->theDeckReturnsTheFollowingCardsInAFIFOOrder1('5,5,10,1');
    }

    /**
     * @Then /^I should see that I won "([^"]*)" games$/
     */
    public function iShouldSeeThatIWonGames(int $numberOfWins)
    {
        $this->commandContext->iShouldSeeInTheCommandOutput("PLAYER (W: {$numberOfWins})");
    }

    /**
     * @Then /^I should see that the dealer has won "([^"]*)" games$/
     */
    public function iShouldSeeThatTheDealerHasWonGames(int $numberOfWins)
    {
        $this->commandContext->iShouldSeeInTheCommandOutput("DEALER (W: {$numberOfWins})");
    }

    /**
     * @Given /^the deck returns the following cards in a FIFO order "([^"]*)"$/
     */
    public function theDeckReturnsTheFollowingCardsInAFIFOOrder1(string $cardsList)
    {
        $cards = explode(',', $cardsList);

        foreach (array_reverse($cards) as $card) {
            $this->deck->addCard(new Card((int) $card));
        }
    }
}
