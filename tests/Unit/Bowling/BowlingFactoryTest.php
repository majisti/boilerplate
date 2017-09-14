<?php

namespace Unit\Bowling;

use Bowling\Frame;
use Bowling\Game;
use Bowling\BowlingFactory;
use Bowling\Roll;
use Tests\Unit\UnitTest;

/**
 * @method BowlingFactory uut()
 */
class BowlingFactoryTest extends UnitTest
{
    const FIRST_ROLL_INDEX_ON_EACH_FRAME = 0;
    const LAST_ROLL_INDEX_ON_LAST_FRAME = 2;

    public function testItShouldCreateAnEmptyGame()
    {
        $game = $this->uut()->createNewGame();

        $this->verifyThat($game, is(anInstanceOf(Game::class)));
        $this->verifyThat($game->getRolls(), is(emptyArray()));
        $this->verifyThat($this->uut()->createNewGame(), is(not(sameInstance($game))));
    }

    public function testItShouldCreatePerfectGame()
    {
        $game = $this->uut()->createPerfectGame();

        $this->verifyThat($game->getRollsCount(), equalTo(Game::MAX_NUMBER_OF_STRIKES_POSSIBLE));

        foreach ($game->getRolls() as $roll) {
            $this->verifyThat("Roll {$roll} should be a strike", $roll->isStrike(), equalTo(true));
        }
    }

    public function testItShouldCreateSpareGame()
    {
        $rollThatIsNotASpare = Roll::EIGHT_PINS();
        $game = $this->uut()->createSpareGame($rollThatIsNotASpare);

        foreach ($game->getFrames() as $frame) {
            foreach ($frame->getRolls() as $index => $roll) {
                if (self::FIRST_ROLL_INDEX_ON_EACH_FRAME === $index || self::LAST_ROLL_INDEX_ON_LAST_FRAME === $index) {
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

    public function testItCanCreateAFrame()
    {
        $frame = $this->uut()->createFrame();
        $this->verifyThat($frame, is(anInstanceOf(Frame::class)));
        $this->verifyThat($this->uut()->createFrame(), is(not(sameInstance($frame))));
    }
}
