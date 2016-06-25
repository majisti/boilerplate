<?php

namespace Bowling;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
interface RollListener
{
    public function onNewRoll(RollEvent $event);
}
