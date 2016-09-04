<?php

namespace Bowling;

use ArrayIterator;
use Bowling\Exception\MaximumScoreExceededException;
use IteratorAggregate;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
class Frame implements IteratorAggregate
{
    const MAX_ROLLS_PER_FRAME = 2;
    const MAX_SCORE_PER_FRAME = 30;

    const FIRST_ROLL = 0;
    const SECOND_ROLL = 1;
    const THIRD_ROLL = 2;

    /**
     * @var Roll[]
     */
    private $rolls = [];

    private $score = 0;

    /**
     * @var bool
     */
    private $isLastFrame = false;

    public function addRoll(Roll $roll)
    {
        if (!$this->isComplete()) {
            $this->rolls[] = $roll;
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
        return count($this->rolls);
    }

    public function isComplete(): bool
    {
        $maxRollsAllowed = self::MAX_ROLLS_PER_FRAME;

        if ($this->isLastFrame() && ($this->hasStrike() || $this->hasSpare())) {
            ++$maxRollsAllowed;
        } elseif ($this->hasStrike()) {
            $maxRollsAllowed = 1;
        }

        return $this->rollCount() >= $maxRollsAllowed;
    }

    /**
     * @return Roll[]
     */
    public function getRolls()
    {
        return $this->rolls;
    }

    public function getRollsCount(): int
    {
        return count($this->getRolls());
    }

    public function hasRolls(): bool
    {
        return $this->getRollsCount() > 0;
    }

    public function setScore(int $score)
    {
        if ($score > static::MAX_SCORE_PER_FRAME) {
            throw MaximumScoreExceededException::create($score, static::MAX_SCORE_PER_FRAME);
        }

        $this->score = $score;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function resetScore()
    {
        $this->setScore(0);
    }

    public function hasStrike(): bool
    {
        foreach ($this->rolls as $roll) {
            if ($roll == Roll::STRIKE()) {
                return true;
            }
        }

        return false;
    }

    public function hasSpare(): bool
    {
        foreach ($this->rolls as $roll) {
            if ($roll == Roll::SPARE()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Roll|null
     */
    public function getRoll(int $rollIndex)
    {
        return $this->getRolls()[$rollIndex] ?? null;
    }

    public function editRoll(int $rollIndex, Roll $newRoll)
    {
        $this->rolls[$rollIndex] = $newRoll;
    }

    public function removeLastRoll()
    {
        unset($this->rolls[$this->getRollsCount() - 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getRolls());
    }
}
