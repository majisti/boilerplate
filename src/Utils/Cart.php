<?php

namespace Utils;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
class Cart
{
    /**
     * @var Calculator
     */
    private $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function addItem()
    {
        $this->calculator->toHtml('foo');
    }
}