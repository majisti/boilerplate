<?php

namespace Tests\Features\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Exception;
use InvalidArgumentException;
use Mockery as m;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Utils\Hamcrest;

class CommandContext extends RawMinkContext implements KernelAwareContext, SnippetAcceptingContext
{
    private $questionHelper;

    const COMMAND_GAME_BLACKJACK = 'majisti:game:blackjack';
    use Hamcrest;
    use KernelDictionary;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var CommandTester
     */
    private $tester;

    /**
     * @var Exception
     */
    private $commandException;

    /**
     * @var array
     */
    private $commandParameters;

    /**
     * @var string
     */
    private $runCommand;

    /**
     * @var int
     */
    private $exitCode;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $this->commandParameters = [];
        $this->exitCode = 0;
    }

    /**
     * @BeforeScenario
     */
    public function initApplication()
    {
        $this->application = new Application();
        $this->initHelperSet();
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @Given /^I run a command "([^"]*)"$/
     */
    public function iRunACommand(string $commandName)
    {
        $command = $this->application->find($commandName);
        $this->tester = new CommandTester($command);

        try {
            $this->exitCode = $this
                ->tester
                ->execute(
                    $this->getCommandParams($commandName)
                )
            ;

            $this->commandException = null;
        } catch (Exception $exception) {
            $this->commandException = $exception;
            $this->exitCode = $exception->getCode();
        }

        $this->runCommand = $commandName;
        $this->commandParameters = array();
    }

    /**
     * @Given /^I run a command "([^"]*)" with parameters:$/
     */
    public function iRunACommandWithParameters($commandName, PyStringNode $parameterJson)
    {
        $this->commandParameters = json_decode($parameterJson->getRaw(), true);

        if (null === $this->commandParameters) {
            throw new InvalidArgumentException(
                'PyStringNode could not be converted to json.'
            );
        }

        $this->iRunACommand($commandName);
    }

    /**
     * @Then /^The command exception "([^"]*)" should be thrown$/
     */
    public function theCommandExceptionShouldBeThrown(string $exceptionClass)
    {
        $this->checkThatCommandHasRun();

        $this->verifyThat($this->commandException, is(anInstanceOf($exceptionClass)));
    }

    /**
     * @Then /^The command exit code should be (\d+)$/
     */
    public function theCommandExitCodeShouldBe($exitCode)
    {
        $this->checkThatCommandHasRun();

        $this->verifyThat($this->exitCode, equalTo($exitCode));
    }

    /**
     * @Then /^I should see "([^"]*)" in the command output$/
     */
    public function iShouldSeeInTheCommandOutput($str)
    {
        $this->checkThatCommandHasRun();

        $this->verifyThat($this->tester->getDisplay(), containsString($str));
    }

    public function getDisplay(): string
    {
        return $this->tester ? $this->tester->getDisplay() : '';
    }

    /**
     * @Then /^The command exception "([^"]*)" with message "([^"]*)" should be thrown$/
     */
    public function theCommandExceptionWithMessageShouldBeThrown($exceptionClass, $exceptionMessage)
    {
        $this->checkThatCommandHasRun();

        $this->verifyThat($this->commandException, is(anInstanceOf($exceptionClass)));
        $this->verifyThat($this->commandException->getMessage(), containsString($exceptionMessage));
    }

    /**
     * @return bool
     *
     * @throws \LogicException
     */
    private function checkThatCommandHasRun()
    {
        if (null === $this->runCommand) {
            throw new \LogicException(
                'You first need to run a command to check to use this step'
            );
        }

        return true;
    }

    /**
     * @param string $command
     *
     * @return array
     */
    private function getCommandParams($command)
    {
        $default = array(
            'command' => $command,
        );

        return array_merge(
            $this->commandParameters,
            $default
        );
    }

    /**
     * @return QuestionHelper|m\MockInterface
     */
    public function getQuestionHelper()
    {
        return $this->questionHelper;
    }

    protected function initHelperSet()
    {
        $this->questionHelper = m::mock(QuestionHelper::class);
        $this->questionHelper->shouldDeferMissing();

        $helperSet = new HelperSet();
        $helperSet->set($this->questionHelper, 'question');

        $this->application->setHelperSet($helperSet);
    }

    /**
     * @Given /^I register the Blackjack command$/
     */
    public function iRegisterTheBlackJackCommand()
    {
        $this->getApplication()->add($this->getContainer()->get('app.command.game.blackjack'));
    }

    /**
     * @When /^I run the blackjack game command$/
     */
    public function iRunTheBlackjackGameCommand()
    {
        $this->iRunACommand(static::COMMAND_GAME_BLACKJACK);
    }
}
