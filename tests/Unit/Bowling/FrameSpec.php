<?php

namespace Unit\Bowling;

use Bowling\Frame;
use Bowling\RollResult;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Frame
 */
class FrameSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Frame::class);
    }

    function it_should_have_a_maximum_of_two_rolls(RollResult $roll)
    {
        $this->rollCount()->shouldEqual(0);
        $this->isComplete()->shouldBe(false);
        $this->addRollResult($roll);
        $this->addRollResult($roll);
        $this->addRollResult($roll);
        $this->rollCount()->shouldEqual(2);
        $this->isComplete()->shouldBe(true);
    }
}
