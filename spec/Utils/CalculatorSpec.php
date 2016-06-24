<?php

namespace spec\Utils;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Utils\Calculator;

class CalculatorSpec extends ObjectBehavior
{
    public function it_is_initialisable()
    {
        $this->shouldHaveType(Calculator::class);
        $this->toHtml('Hello')->shouldReturn("<p>Hello</p>");
    }
}
