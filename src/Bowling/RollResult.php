<?php

namespace Bowling;

use MyCLabs\Enum\Enum;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 *
 * @method static RollResult GUTTER()
 * @method static RollResult ONE_PIN()
 * @method static RollResult TWO_PINS()
 * @method static RollResult THREE_PINS()
 * @method static RollResult FOUR_PINS()
 * @method static RollResult FIVE_PINS()
 * @method static RollResult SIX_PINS()
 * @method static RollResult SEVEN_PINS()
 * @method static RollResult EIGHT_PINS()
 * @method static RollResult NINE_PINS()
 * @method static RollResult STRIKE()
 * @method static RollResult SPARE()
 */
class RollResult extends Enum
{
    const GUTTER = 0;
    const ONE_PIN = 1;
    const TWO_PINS = 2;
    const THREE_PINS = 3;
    const FOUR_PINS = 4;
    const FIVE_PINS = 5;
    const SIX_PINS = 6;
    const SEVEN_PINS = 7;
    const EIGHT_PINS = 8;
    const NINE_PINS = 9;
    const STRIKE = 10;
    const SPARE = 'spare';

    public function isStrike(): bool
    {
        return $this->getValue() === self::STRIKE;
    }

    public function isSpare(): bool
    {
        return $this->getValue() === self::SPARE;
    }

    public function isGutter(): bool
    {
        return $this->getValue() === self::GUTTER;
    }

    public function isEqual(RollResult $roll): bool
    {
        return $this == $roll;
    }
}
