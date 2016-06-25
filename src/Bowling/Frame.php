<?php

namespace Bowling;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
class Frame
{
    const MAX_ROLLS_PER_FRAME = 2;

    /**
     * @var RollResult[]
     */
    private $rollResults = [];

    /**
     * @var bool
     */
    private $isLastFrame = false;

    public function addRollResult(RollResult $roll)
    {
        if (!$this->isComplete()) {
            $this->rollResults[] = $roll;
        }
    }

    public function isLastFrame(): bool
    {
        return $this->isLastFrame;
    }

    public function setAsLastFrame()
    {
        $this->isLastFrame = true;
    }

    public function rollCount(): int
    {
        return count($this->rollResults);
    }

    public function isComplete(): bool
    {
        $maxRollsAllowed = self::MAX_ROLLS_PER_FRAME;

        if ($this->isLastFrame() && ($this->hasStrike() || $this->hasSpare())) {
            $maxRollsAllowed++;
        } elseif ($this->hasStrike()) {
            $maxRollsAllowed = 1;
        }

        return $this->rollCount() >= $maxRollsAllowed;
    }

    /**
     * @return RollResult[]
     */
    public function getRolls()
    {
        return $this->rollResults;
    }

    private function hasStrike(): bool
    {
        foreach ($this->rollResults as $roll) {
            if ($roll == RollResult::STRIKE()) {
                return true;
            }
        }

        return false;
    }

    private function hasSpare(): bool
    {
        foreach ($this->rollResults as $roll) {
            if ($roll == RollResult::SPARE()) {
                return true;
            }
        }

        return false;
    }
}