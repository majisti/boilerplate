<?php

namespace Bowling;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
class RollEvent
{
    /**
     * @var Frame
     */
    private $frame;

    /**
     * @var RollResult
     */
    private $rollResult;

    public function __construct(Frame $frame, RollResult $rollResult)
    {
        $this->frame = $frame;
        $this->rollResult = $rollResult;
    }

    public function getFrame(): Frame
    {
        return $this->frame;
    }

    public function getRollResult(): RollResult
    {
        return $this->rollResult;
    }
}
