<?php

namespace Tests\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Blackjack\Card;
use Blackjack\Deck;
use Blackjack\DeckBuilder;
use Blackjack\GameCoordinator;
use Mockery as m;
use PSS\Behat\Symfony2MockerExtension\ServiceMocker;
use Tests\Utils\Hamcrest;

class BlackjackContext implements Context
{
    use KernelDictionary;
    use Hamcrest;

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
            ->atLeast()->once()
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
     * @Given /^the deck returns the following cards in a FILO order:$/
     */
    public function theDeckReturnsTheFollowingCardsInAFILOOrder(TableNode $cardsTable)
    {
        foreach (array_reverse($cardsTable->getRows()) as $row) {
            $this->deck->addCard(new Card($row[0]));
        }
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
     * @When /^I neither will have to hit nor stand$/
     */
    public function iNeitherWillHaveToHitNorStand()
    {
        $this->commandContext->getQuestionHelper()->shouldReceive('ask')->never();
    }

    /**
     * @Then /^I should be asked for a rematch and answer "([^"]*)"$/
     */
    public function iShouldBeAskedForARematchAndAnswer(string $answer)
    {
        $question = 'Do you want to play again? [y/n]';

        $this->commandContext->getQuestionHelper()
            ->shouldReceive('ask')
            ->once()
            ->with(containsString($question))
            ->andReturn($answer);
    }
}
