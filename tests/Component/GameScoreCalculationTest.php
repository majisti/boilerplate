<?php

namespace Tests\Component;

use Bowling\GameFactory;
use Bowling\RollResult;
use Bowling\ScoreListener;

/**
 * @group bowling
 */
class GameScoreCalculationTest extends ComponentTest
{
    /**
     * @var GameFactory
     */
    private $gameFactory;

    public function _before()
    {
        $this->gameFactory = new GameFactory();
    }

    /**
     * @param RollResult[] $rolls
     * @param int $expectedResult
     *
     * @dataProvider getRolls
     */
    public function testItCalculatesScoreForAGame(array $rolls, int $expectedResult)
    {
        $this->gameFactory = new GameFactory();
        $game = $this->gameFactory->createNewGame();
        $game->addRollListener(new ScoreListener());

        foreach ($rolls as $roll) {
            $game->roll($roll);
        }

        $this->verifyThat($game->getCurrentScore(), equalTo($expectedResult));
    }

    public function getRolls()
    {
        $gameFactory = new GameFactory();

        yield [[RollResult::ONE_PIN(), RollResult::TWO_PINS()], 3];
        yield [[RollResult::STRIKE(), RollResult::TWO_PINS(), RollResult::THREE_PINS()], 20];
        yield [[RollResult::STRIKE(), RollResult::STRIKE(), RollResult::THREE_PINS(), RollResult::FIVE_PINS()], 49];
        yield [[RollResult::STRIKE(), RollResult::ONE_PIN(), RollResult::SPARE(), RollResult::FIVE_PINS()], 40];
        yield [[RollResult::STRIKE(), RollResult::STRIKE(), RollResult::EIGHT_PINS(),
            RollResult::SPARE(), RollResult::STRIKE(), RollResult::FIVE_PINS(), RollResult::SPARE()], 98];
        yield [$gameFactory->createSpareGame(RollResult::NINE_PINS())->getRolls(), 190];
        yield [$gameFactory->createPerfectGame()->getRolls(), 300];
        yield [$gameFactory->createGutterGame()->getRolls(), 0];
    }
}
