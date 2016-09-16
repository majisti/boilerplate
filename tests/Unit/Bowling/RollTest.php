<?php

namespace Unit\Bowling;

use Bowling\Game;
use Bowling\Roll;
use Tests\Unit\UnitTest;

/**
 * @method Game uut()
 */
class RollTest extends UnitTest
{
    public function testItCompareRollsForStrikeOrSpareOrGutter()
    {
        $roll = Roll::STRIKE();
        $this->verifyThat($roll->isGutter(), equalTo(false));
        $this->verifyThat($roll->isSpare(), equalTo(false));
        $this->verifyThat($roll->isStrike(), equalTo(true));

        $roll = Roll::SPARE();
        $this->verifyThat($roll->isGutter(), equalTo(false));
        $this->verifyThat($roll->isStrike(), equalTo(false));
        $this->verifyThat($roll->isSpare(), equalTo(true));

        $roll = Roll::GUTTER();
        $this->verifyThat($roll->isStrike(), equalTo(false));
        $this->verifyThat($roll->isSpare(), equalTo(false));
        $this->verifyThat($roll->isEqual(Roll::EIGHT_PINS()), equalTo(false));
        $this->verifyThat($roll->isGutter(), equalTo(true));
    }

    public function testItShouldHaveStringRepresentation()
    {
        $this->verifyThat((string) Roll::EIGHT_PINS(), equalTo('8'));
    }

    public function testItShouldSupportRollEquality()
    {
        $roll = Roll::EIGHT_PINS();
        $equalRole = Roll::EIGHT_PINS();
        $unequalRole = Roll::FIVE_PINS();

        $this->verifyThat($roll->isEqual($equalRole), equalTo(true));
        $this->verifyThat($roll->isEqual($unequalRole), equalTo(false));
    }
}
