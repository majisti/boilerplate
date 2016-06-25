<?php

namespace Unit\Bowling;

use Bowling\Frame;
use Bowling\FrameFactory;
use Bowling\Game;
use Bowling\RollResult;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Game
 */
class GameSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Game::class);
    }

    function it_creates_a_new_frame_on_each_two_roll(FrameFactory $factory, Frame $frame)
    {
        $this->setFrameFactory($factory);
        $factory->createFrame()->shouldBeCalledTimes(2)->willReturn($frame);

        $frame->addRollResult(Argument::type(RollResult::class))->shouldBeCalled();
        $frame->isComplete()->shouldBeCalled()->willReturn(false, true);

        $this->roll(RollResult::THREE_PINS());
        $this->roll(RollResult::FOUR_PINS());
        $this->roll(RollResult::FOUR_PINS());

        $this->getFrames()->shouldHaveCount(2);
    }
}
