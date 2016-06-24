<?php

namespace spec\Utils;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophet;
use Spec;
use Utils\Calculator;
use Utils\Cart;
use Mockery as m;

/**
 * @mixin Cart
 */
class CartSpec extends Spec
{
    function let(Calculator $calculator)
    {
        $this->beConstructedWith($calculator);
    }

    function it_is_initialisable()
    {
        $this->shouldHaveType(Cart::class);
    }

    function it_has_object_interaction(Calculator $calculator)
    {
        $calculator->toHtml(Argument::any())->shouldBeCalled();
        $this->addItem();
    }

    function it_test_mockery()
    {
        $cart = m::mock(Cart::class);
    }
}
