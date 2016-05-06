<?php

namespace Tests\Codeception\TestCase;

use Codeception\Module\Symfony2;
use Codeception\TestCase\Test;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Tests\DataConfiguration;
use Tests\FeatureTester;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\Routing\Router;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
abstract class FeatureTest extends Test
{
    use Hamcrest;

    /**
     * @var FeatureTester
     */
    protected $tester;

    public function getRouter(): Router
    {
        return $this->getContainer()->get('router');
    }

    public function getSymfonyClient(): Client
    {
        return $this->getSymfony2()->client;
    }

    public function getSymfony2(): Symfony2
    {
        return $this->getModule(DataConfiguration::HELPER_SYMFONY2);
    }

    public function getContainer(): MockerContainer
    {
        return $this->getSymfony2()->container;
    }
}
