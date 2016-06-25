<?php

namespace Unit\Bowling;

use Bowling\Game;
use Bowling\RollResult;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Game uut()
 */
class RollResultTest extends UnitTest
{
    public function testItCompareRollsForStrikeOrSpareOrGutter()
    {
        $roll = RollResult::STRIKE();
        $this->verifyThat($roll->isGutter(), equalTo(false));
        $this->verifyThat($roll->isSpare(), equalTo(false));
        $this->verifyThat($roll->isStrike(), equalTo(true));

        $roll = RollResult::SPARE();
        $this->verifyThat($roll->isGutter(), equalTo(false));
        $this->verifyThat($roll->isStrike(), equalTo(false));
        $this->verifyThat($roll->isSpare(), equalTo(true));

        $roll = RollResult::GUTTER();
        $this->verifyThat($roll->isStrike(), equalTo(false));
        $this->verifyThat($roll->isSpare(), equalTo(false));
        $this->verifyThat($roll->isEqual(RollResult::EIGHT_PINS()), equalTo(false));
        $this->verifyThat($roll->isGutter(), equalTo(true));
    }

    public function testItShouldHaveStringRepresentation()
    {
        $this->verifyThat((string)RollResult::EIGHT_PINS(), equalTo('8'));
    }

    public function testItShouldSupportRollEquality()
    {
        $roll = RollResult::EIGHT_PINS();
        $equalRole = RollResult::EIGHT_PINS();
        $unequalRole = RollResult::FIVE_PINS();

        $this->verifyThat($roll->isEqual($equalRole), equalTo(true));
        $this->verifyThat($roll->isEqual($unequalRole), equalTo(false));
    }
}
