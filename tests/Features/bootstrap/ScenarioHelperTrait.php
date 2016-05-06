<?php

use Behat\Behat\Hook\Scope\StepScope;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\StepNode;

trait ScenarioHelperTrait
{
    protected function getScenario(StepScope $scope): ScenarioInterface
    {
        $scenarios = $scope->getFeature()->getScenarios();
        foreach ($scenarios as $scenario) {
            $stepLinesInScenario = array_map(
                function (StepNode $step) {
                    return $step->getLine();
                },
                $scenario->getSteps()
            );

            if (in_array($scope->getStep()->getLine(), $stepLinesInScenario)) {
                return $scenario;
            }
        }

        throw new \LogicException('Unable to find the scenario');
    }
}
