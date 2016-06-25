<?php

namespace Unit\Bowling;

use Bowling\Frame;
use Bowling\FrameFactory;
use Bowling\Game;
use Bowling\RollResult;
use Tests\Unit\UnitTest;
use Mockery as m;

/**
 * @method Game uut()
 */
class GameTest extends UnitTest
{
    /**
     * @var Frame|m\MockInterface
     */
    private $frame;

    /**
     * @var FrameFactory|m\MockInterface
     */
    private $frameFactory;

    public function setUp()
    {
        $this->frame = m::spy(Frame::class);
        $this->frameFactory = m::spy(FrameFactory::class);

        $this->frameFactory
            ->shouldReceive('createFrame')
            ->andReturn($this->frame)
            ->byDefault()
        ;
        $this->uut()->setFrameFactory($this->frameFactory);
    }

    public function testItCreatesANewFrameWhenItIsCompleted()
    {
        $this->frame->shouldReceive('isComplete')->andReturn(false, false, true);

        $this->uut()->roll(RollResult::ONE_PIN());
        $this->uut()->roll(RollResult::ONE_PIN());
        $this->uut()->roll(RollResult::ONE_PIN());

        $this->verifyThat(count($this->uut()->getFrames()), equalTo(2));
    }

    public function testItReturnsAllRolls()
    {
        $this->frame->shouldReceive('getRolls')->andReturn([RollResult::GUTTER()]);
        $this->frame->shouldReceive('isComplete')->andReturn(false, true);

        $rollCount = 10;

        //todo: gutter game?
        for ($i = 0; $i < $rollCount; $i++) {
            $this->uut()->roll(RollResult::GUTTER());
        }

        $this->verifyThat(count($this->uut()->getRolls()), equalTo($rollCount));
    }

    public function testShouldHaveAMaximumOfTenFrames()
    {
        $this->frame->shouldReceive('isComplete')->andReturn(false, true, true, true, true, true, true, true, true, true, false);
        $this->frame->shouldReceive('isLastFrame')->andReturn(false, true);
        $this->frame->shouldReceive('setAsLastFrame')->once();

        //todo: reuse perfect game
        for ($i = 0; $i < Game::MAX_NUMBER_OF_STRIKES_POSSIBLE; $i++) {
            $this->uut()->roll(RollResult::STRIKE());
        }

        $this->verifyThat($this->uut()->getFramesCount(), equalTo(Game::MAX_NUMBER_OF_FRAMES_POSSIBLE));
    }
}
