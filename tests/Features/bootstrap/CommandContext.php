<?php

use Behat\Behat\Context\Context;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

class CommandContext implements Context
{
    private $application;

    /** @var CommandTester */
    private $tester;

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
        $this->tester = new CommandTester($command);

        $this->tester->execute(array('command' => $command->getName()));
    }

    /**
     * @Then the exit code should be :code
     */
    public function theExitCodeShouldBe($code)
    {
        //todo: use hamcrest here?
        $this->assertEquals($code, $this->tester->getStatusCode());
    }
}
