<?php

namespace Bowling;

use MyCLabs\Enum\Enum;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 *
 * @method static Roll GUTTER()
 * @method static Roll ONE_PIN()
 * @method static Roll TWO_PINS()
 * @method static Roll THREE_PINS()
 * @method static Roll FOUR_PINS()
 * @method static Roll FIVE_PINS()
 * @method static Roll SIX_PINS()
 * @method static Roll SEVEN_PINS()
 * @method static Roll EIGHT_PINS()
 * @method static Roll NINE_PINS()
 * @method static Roll STRIKE()
 * @method static Roll SPARE()
 */
class Roll extends Enum
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
    const TEN_PINS = 10;
    const STRIKE = self::TEN_PINS;
    const SPARE = 'spare';

    public function isStrike(): bool
    {
        return self::STRIKE === $this->getValue();
    }

    public function isSpare(): bool
    {
        return self::SPARE === $this->getValue();
    }

    public function isGutter(): bool
    {
        return self::GUTTER === $this->getValue();
    }

    public function isEqual(Roll $roll): bool
    {
        return $this == $roll;
    }
}
