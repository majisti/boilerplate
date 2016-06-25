<?php

namespace Integration;

use Bowling\Game;
use Bowling\RollResult;
use Bowling\ScoreCalculator;
use Tests\Codeception\TestCase\IntegrationTest;

/**
 * @group bowling
 */
class GameScoreCalculationTest extends IntegrationTest
{
    /**
     * @var ScoreCalculator
     */
    private $calculator;

    public function setUp()
    {
        $this->calculator = new ScoreCalculator();
    }

    /**
     * @param RollResult[] $rolls
     * @param int $expectedResult
     *
     * @dataProvider getRolls
     */
    public function testItCalculatesScoreForAGame(array $rolls, int $expectedResult)
    {
        $game = new Game();

        foreach ($rolls as $roll) {
            $game->roll($roll);
        }

        $this->verifyThat($this->calculator->calculateScoreForGame($game), equalTo($expectedResult));
    }

    public function getRolls()
    {
//        yield [[RollResult::ONE_PIN(), RollResult::TWO_PINS()], 3];
//        yield [[RollResult::STRIKE(), RollResult::TWO_PINS(), RollResult::THREE_PINS()], 20];
//        yield [[RollResult::STRIKE(), RollResult::STRIKE(), RollResult::THREE_PINS(), RollResult::FIVE_PINS()], 49];
//        yield [[RollResult::STRIKE(), RollResult::ONE_PIN(), RollResult::SPARE(), RollResult::FIVE_PINS()], 40];
        yield [$this->perfectGame(), 300];
    }

    /**
     * @return RollResult[]
     */
    private function perfectGame()
    {
        $rolls = [];
        for ($i = 0; $i < Game::MAX_NUMBER_OF_STRIKES_POSSIBLE; $i++) {
            $rolls[] = RollResult::STRIKE();
        }

        return $rolls;
    }
}
