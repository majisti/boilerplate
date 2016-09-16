<?php

namespace Tests\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\KeywordNodeInterface;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\Context\MinkContext;
use Sanpi\Behatch\Context\DebugContext as DebugContext;
use SplFileInfo;
use Symfony\Component\Console\Input\ArgvInput;
use Tests\Features\Utils\ScenarioHelperTrait;

class ScreenshotContext implements Context, SnippetAcceptingContext
{
    use ScenarioHelperTrait;

    /**
     * @var string
     */
    protected $screenshotDir;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var MinkContext
     */
    protected $minkContext;

    /**
     * @var DebugContext
     */
    protected $debugContext;

    public function __construct($screenshotDir)
    {
        $this->screenshotDir = $screenshotDir;

        if (!file_exists($screenshotDir)) {
            mkdir($screenshotDir);
        }
    }

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        /* @var $env InitializedContextEnvironment  */
        $env = $scope->getEnvironment();

        $this->minkContext = $env->getContext(MinkContext::class);
        $this->debugContext = $env->getContext(DebugContext::class);
    }

    /**
     * @Then I take a screenshot named :name
     */
    public function iTakeAScreenshotNamed($name)
    {
        if ($this->isSeleniumDriver()) {
            $this->debugContext->saveScreenshot(
                sprintf('%s_%s.png',
                    $this->getCurrentProfile(),
                    $name
                ),
                $this->screenshotDir
            );
        }
    }

    /**
     * @AfterStep
     *
     * @param AfterStepScope $scope
     */
    public function takeScreenshotOnStepFailure(AfterStepScope $scope)
    {
        if (!$scope->getTestResult()->isPassed()) {
            if ($this->isSeleniumDriver()) {
                $scenario = $this->getScenario($scope);
                $this->debugContext->saveScreenshot(
                    sprintf('fail__%s_%s__%s.png',
                        $this->getCurrentProfile(),
                        $this->getCanonicalizedFeatureName($scope->getFeature()),
                        $this->getCanonicalizedTitle($scenario)
                    ),
                    $this->screenshotDir
                );
            }
        }
    }

    protected function getCurrentProfile(): string
    {
        $input = new ArgvInput($_SERVER['argv']);

        return $input->getParameterOption(array('--profile', '-p')) ?: 'default';
    }

    protected function getCanonicalizedFeatureName(FeatureNode $feature): string
    {
        $featureFile = new SplFileInfo($feature->getFile());
        $featureRelativeDirectoryPath = str_replace($this->basePath.'/features/', '', $featureFile->getPath());

        return $this->canonicalizeName("{$featureRelativeDirectoryPath} {$featureFile->getFilename()}");
    }

    protected function getCanonicalizedTitle(KeywordNodeInterface $node): string
    {
        return $this->canonicalizeName($node->getTitle());
    }

    protected function canonicalizeName(string $str): string
    {
        return strtolower(str_replace(array(' ', '/'), '_', $str));
    }

    protected function isSeleniumDriver(): bool
    {
        return $this->minkContext->getSession()->getDriver() instanceof Selenium2Driver;
    }
}
