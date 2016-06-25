<?php

namespace Unit\Bowling;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FrameFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Bowling\FrameFactory');
    }
}
