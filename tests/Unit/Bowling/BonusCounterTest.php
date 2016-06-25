<?php

namespace Unit\Bowling;

use Bowling\BonusCounter;
use Bowling\Frame;
use LogicException;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method BonusCounter uut()
 */
class BonusCounterTest extends UnitTest
{
    public function testItShouldThrowLogicExceptionOnZeroCount()
    {
        $this->expectException(LogicException::class);
        new BonusCounter(new Frame(), 0);
    }

    protected function createUnitUnderTest()
    {
        return new BonusCounter(new Frame(), 2);
    }

    public function testItDecrementsCount()
    {
        $this->verifyThat($this->uut()->hasBonusRolls(), equalTo(true));
        $this->uut()->decrement();

        $this->verifyThat($this->uut()->hasBonusRolls(), equalTo(true));
        $this->uut()->decrement();

        $this->verifyThat($this->uut()->hasBonusRolls(), equalTo(false));
    }
}
