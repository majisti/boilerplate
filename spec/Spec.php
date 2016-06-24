<?php

use PhpSpec\ObjectBehavior;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
class Spec extends ObjectBehavior
{
    protected function uut()
    {
        return $this->getWrappedObject();
    }
}