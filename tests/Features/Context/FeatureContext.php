<?php

namespace Tests\Features\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Driver\Selenium2Driver;
use Sanpi\Behatch\Context\BaseContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends BaseContext implements SnippetAcceptingContext
{
    const DESKTOP_SIZE = array('width' => 1920, 'height' => 1080);
    const CURRENT_WINDOW_SIZE = self::DESKTOP_SIZE;

    /**
     * @BeforeScenario
     */
    public function resizeWindow()
    {
        if ($this->getSession()->getDriver() instanceof Selenium2Driver) {
            $this->getSession()->resizeWindow(
                static::CURRENT_WINDOW_SIZE['width'],
                static::CURRENT_WINDOW_SIZE['height']
            );
        }
    }

    /**
     * @AfterStep
     *
     * @param AfterStepScope $scope
     */
    public function saveLastResponseOnFailure(AfterStepScope $scope)
    {
        if (!$scope->getTestResult()->isPassed()) {
            $this->getMinkContext()->showLastResponse();
        }
    }
}
