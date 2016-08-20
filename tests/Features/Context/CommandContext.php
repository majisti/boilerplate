<?php

namespace Tests\Features\Context;

use Behat\Behat\Context\Context;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\Utils\Hamcrest;

class CommandContext implements Context
{
    use Hamcrest;

    private $application;

    /**
     * @var CommandTester
     */
    private $commandTester;

    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
    }

    /**
     * @When I execute the symfony command :name
     */
    public function iExecuteTheSymfonyCommand($name)
    {
        $command = $this->application->find($name);

        $this->commandTester = new CommandTester($command);
        $this->commandTester->execute(array('command' => $command->getName()));
    }

    /**
     * @Then the exit code should be :code
     */
    public function theExitCodeShouldBe($code)
    {
        $this->verifyThat($code, equalTo($this->commandTester->getStatusCode()));
    }
}
