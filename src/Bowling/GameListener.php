<?php

namespace Bowling;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
interface GameListener
{
    public function onNewRoll(GameEvent $event);
    public function onNewFrame(GameEvent $event);
}
