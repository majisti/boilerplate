<?php

namespace Tests\Component;

use Bowling\BowlingFactory;
use Bowling\Player;
use Bowling\Roll;
use Bowling\ScoreCalculationSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @group bowling
 */
class GameScoreCalculationTest extends ComponentTest
{
    /**
     * @var BowlingFactory
     */
    private $gameFactory;

    public function _before()
    {
        $this->gameFactory = new BowlingFactory();
    }

    /**
     * @param Roll[] $rolls
     * @param int $expectedResult
     *
     * @dataProvider getRolls
     */
    public function testItCalculatesScoreForAGame(array $rolls, int $expectedResult)
    {
        $this->gameFactory = new BowlingFactory();

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new ScoreCalculationSubscriber());

        $game = $this->gameFactory->createNewGame();
        $game->setEventDispatcher($dispatcher);
        
        $player = new Player($game);

        foreach ($rolls as $roll) {
            $player->throw($roll);
        }

        $this->verifyThat($game->getCurrentScore(), equalTo($expectedResult));
    }

    public function getRolls()
    {
        $gameFactory = new BowlingFactory();

        yield [[Roll::ONE_PIN(), Roll::TWO_PINS()], 3];
        yield [[Roll::STRIKE(), Roll::TWO_PINS(), Roll::THREE_PINS()], 20];
        yield [[Roll::STRIKE(), Roll::STRIKE(), Roll::THREE_PINS(), Roll::FIVE_PINS()], 49];
        yield [[Roll::STRIKE(), Roll::ONE_PIN(), Roll::SPARE(), Roll::FIVE_PINS()], 40];
        yield [[Roll::STRIKE(), Roll::STRIKE(), Roll::EIGHT_PINS(),
            Roll::SPARE(), Roll::STRIKE(), Roll::FIVE_PINS(), Roll::SPARE()], 98];
        yield [$gameFactory->createSpareGame(Roll::NINE_PINS())->getRolls(), 190];
        yield [$gameFactory->createPerfectGame()->getRolls(), 300];
        yield [$gameFactory->createGutterGame()->getRolls(), 0];
    }
}
