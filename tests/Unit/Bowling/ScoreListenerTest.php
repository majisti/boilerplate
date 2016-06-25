<?php

namespace Unit\Bowling;

use Bowling\ScoreListener;
use Bowling\Frame;
use Bowling\RollEvent;
use Bowling\RollResult;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method ScoreListener uut()
 */
class ScoreListenerTest extends UnitTest
{
    public function testItAddsBonusScoreForNextTwoRollsOnStrike()
    {
        $firstFrame = m::spy(Frame::class);
        $secondFrame = m::spy(Frame::class);

        $firstFrame->shouldReceive('addToScore')->once()->with(RollResult::TWO_PINS);
        $firstFrame->shouldReceive('addToScore')->once()->with(RollResult::THREE_PINS);

        $this->uut()->onNewRoll(new RollEvent($firstFrame, RollResult::STRIKE()));
        $this->uut()->onNewRoll(new RollEvent($secondFrame, RollResult::TWO_PINS()));
        $this->uut()->onNewRoll(new RollEvent($secondFrame, RollResult::THREE_PINS()));
    }

    public function testItAddBonusScoreForTheNextRollOnlyOnSpare()
    {
        $firstFrame = m::spy(Frame::class);
        $secondFrame = m::spy(Frame::class);

        $firstFrame->shouldReceive('addToScore')->times(3);
        $secondFrame->shouldReceive('addToScore')->times(2);

        $this->uut()->onNewRoll(new RollEvent($firstFrame, RollResult::ONE_PIN()));
        $this->uut()->onNewRoll(new RollEvent($firstFrame, RollResult::SPARE()));
        $this->uut()->onNewRoll(new RollEvent($secondFrame, RollResult::THREE_PINS()));
        $this->uut()->onNewRoll(new RollEvent($secondFrame, RollResult::FOUR_PINS()));
    }

    public function testItShouldNotAddBonusesOnLastFrame()
    {
        $frame = m::spy(Frame::class);
        $frame->shouldReceive('isLastFrame')->andReturn(true);
        $frame->shouldReceive('addToScore')->times(3);

        $this->uut()->onNewRoll(new RollEvent($frame, RollResult::STRIKE()));
        $this->uut()->onNewRoll(new RollEvent($frame, RollResult::NINE_PINS()));
        $this->uut()->onNewRoll(new RollEvent($frame, RollResult::SPARE()));
    }

    public function testItShouldAddScoreToFrameOnRoll()
    {
        $frame = m::spy(Frame::class);
        $frame->shouldReceive('addToScore')->once()->with(RollResult::EIGHT_PINS);

        $this->uut()->onNewRoll(new RollEvent($frame, RollResult::EIGHT_PINS()));
    }
}
