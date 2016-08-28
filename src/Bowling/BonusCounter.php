<?php

namespace Bowling;

use LogicException;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
class BonusCounter
{
    const BONUS_ROLLS_FOR_SPARE = 1;
    const BONUS_ROLLS_FOR_STRIKE = 2;

    /**
     * @var Frame
     */
    private $frame;

    /**
     * @var int
     */
    private $bonusRollCount;

    public function __construct(Frame $frame, int $bonusRollCount)
    {
        if ($bonusRollCount <= 0) {
            throw new LogicException('Count cannot be zero or negative');
        }

        $this->frame = $frame;
        $this->bonusRollCount = $bonusRollCount;
    }

    public function getFrame(): Frame
    {
        return $this->frame;
    }

    public function hasBonusRolls(): bool
    {
        return $this->getBonusRollCount() > 0;
    }

    public function getBonusRollCount(): int
    {
        return $this->bonusRollCount;
    }

    public function decrement()
    {
        --$this->bonusRollCount;
    }
}
