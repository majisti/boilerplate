<?php

namespace Unit\Bowling;

use Bowling\Game;
use Bowling\GameFactory;
use Bowling\RollResult;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method GameFactory uut()
 */
class GameFactoryTest extends UnitTest
{
    const FIRST_ROLL_INDEX_ON_EACH_FRAME = 0;
    const LAST_ROLL_INDEX_ON_LAST_FRAME = 2;

    public function testItShouldCreateAnEmptyGame()
    {
        $game = $this->uut()->createNewGame();

        $this->verifyThat($game, is(anInstanceOf(Game::class)));
        $this->verifyThat($game->getRolls(), is(emptyArray()));
    }

    public function testItShouldCreatePerfectGame()
    {
        $game = $this->uut()->createPerfectGame();

        $this->verifyThat(count($game->getRolls()), equalTo(Game::MAX_NUMBER_OF_STRIKES_POSSIBLE));

        foreach ($game->getRolls() as $roll) {
            $this->verifyThat("Roll {$roll} should be a strike", $roll->isStrike(), equalTo(true));
        }
    }

    public function testItShouldCreateSpareGame()
    {
        $rollThatIsNotASpare = RollResult::EIGHT_PINS();
        $game = $this->uut()->createSpareGame($rollThatIsNotASpare);

        foreach ($game->getFrames() as $frame) {
            foreach ($frame->getRolls() as $index => $roll) {
                if ($index === self::FIRST_ROLL_INDEX_ON_EACH_FRAME || $index === self::LAST_ROLL_INDEX_ON_LAST_FRAME) {
                    $this->verifyThat($roll->isEqual($rollThatIsNotASpare));
                    continue;
                }

                $this->verifyThat("Roll {$roll} should be a spare", $roll->isSpare(), equalTo(true));
            }
        }
    }

    public function testItShouldCreateGutterGame()
    {
        $game = $this->uut()->createGutterGame();

        foreach ($game->getRolls() as $roll) {
            $this->verifyThat("Roll {$roll} should be a gutter", $roll->isGutter(), equalTo(true));
        }
    }
}
