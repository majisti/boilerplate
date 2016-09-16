<?php

namespace Unit\Bowling;

use Bowling\Frame;
use Bowling\Game;
use Bowling\Roll;
use Bowling\ScoreCalculator;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method ScoreCalculator uut()
 */
class ScoreCalculatorTest extends UnitTest
{
    /**
     * @var Game|m\MockInterface
     */
    private $game;

    /**
     * @var Frame|m\MockInterface
     */
    private $frame;

    public function setUp()
    {
        $this->game = m::spy(Game::class);
        $this->frame = m::spy(Frame::class);
    }

    public function createUnitUnderTest()
    {
        return new ScoreCalculator();
    }

    public function testItAddsBonusScoreForNextTwoRollsOnStrike()
    {
        $firstFrame = $this->createFrame([Roll::STRIKE()]);
        $secondFrame = $this->createFrame([Roll::TWO_PINS(), Roll::THREE_PINS()]);

        $this->game->shouldReceive('getFrames')->andReturn([$firstFrame, $secondFrame]);

        $this->uut()->calculateGameScore($this->game);

        $this->verifyThat($firstFrame->getScore(), equalTo(10 + 2 + 3));
        $this->verifyThat($secondFrame->getScore(), equalTo(2 + 3));
    }

    public function testItAddsBonusScoreForTheNextRollOnlyOnSpare()
    {
        $firstFrame = $this->createFrame([Roll::ONE_PIN(), Roll::SPARE()]);
        $secondFrame = $this->createFrame([Roll::THREE_PINS(), Roll::FOUR_PINS()]);

        $this->game->shouldReceive('getFrames')->andReturn([$firstFrame, $secondFrame]);

        $this->uut()->calculateGameScore($this->game);

        $this->verifyThat($firstFrame->getScore(), equalTo(10 + 3));
        $this->verifyThat($secondFrame->getScore(), equalTo(3 + 4));
    }

    public function testItShouldNotAddBonusesOnLastFrame()
    {
        $frame = $this->createFrame([Roll::STRIKE(), Roll::ONE_PIN(), Roll::SPARE()], true);

        $this->game->shouldReceive('getFrames')->andReturn([$frame]);

        $this->uut()->calculateGameScore($this->game);

        $this->verifyThat($frame->getScore(), equalTo(20));
    }

    public function testCalculatesScoreForAGivenFrame()
    {
        $frame = $this->createFrame([Roll::THREE_PINS()]);

        $this->game->shouldReceive('getFrames')->andReturn([$frame]);

        $this->uut()->calculateGameScore($this->game);

        $this->verifyThat($frame->getScore(), equalTo(Roll::THREE_PINS));
    }

    public function testRecalculatesScoreAtGivenFrameIndex()
    {
        $firstFrame = $this->createFrame([Roll::THREE_PINS(), Roll::SPARE()]);
        $secondFrame = $this->createFrame([Roll::ONE_PIN()]);

        $this->game->shouldReceive('getFrames')
            ->andReturn([$firstFrame, $secondFrame], [$firstFrame, $secondFrame]);

        $this->uut()->calculateGameScore($this->game);

        $secondFrame->editRoll(Frame::FIRST_ROLL, Roll::FOUR_PINS());
        $this->uut()->calculateGameScore($this->game);

        $this->verifyThat($firstFrame->getScore(), equalTo(3 + 7 + 4));
        $this->verifyThat($secondFrame->getScore(), equalTo(4));
    }

    /**
     * @param Roll[] $withRolls
     */
    private function createFrame(array $withRolls = [], bool $isLastFrame = false): Frame
    {
        $frame = new Frame();

        if ($isLastFrame) {
            $frame->setAsLastFrame();
        }

        foreach ($withRolls as $roll) {
            $frame->addRoll($roll);
        }

        return $frame;
    }
}
