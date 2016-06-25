<?php

namespace Unit\Bowling;

use Bowling\Frame;
use Bowling\RollResult;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Frame uut()
 */
class FrameTest extends UnitTest
{
    public function testItShouldHaveAMaximumOfTwoRolls()
    {
        $this->verifyThat($this->uut()->isComplete(), equalTo(false));

        $this->uut()->addRollResult(RollResult::ONE_PIN());
        $this->verifyThat($this->uut()->isComplete(), equalTo(false));

        $this->uut()->addRollResult(RollResult::SPARE());
        $this->verifyThat($this->uut()->isComplete(), equalTo(true));
    }

    public function testItShouldBeCompletedWhenAStrikeResultIsAdded()
    {
        $this->uut()->addRollResult(RollResult::STRIKE());
        $this->uut()->addRollResult(RollResult::STRIKE());
        $this->verifyThat($this->uut()->isComplete(), equalTo(true));
        $this->verifyThat($this->uut()->rollCount(), equalTo(1));
    }

    public function testLastFrameCanHaveABonusRollsIfTenPinsAreKnockedDownInTheFirstTwoRolls()
    {
        $frame = $this->createUnitUnderTest();
        $frame->setAsLastFrame();

        $frame->addRollResult(RollResult::ONE_PIN());
        $frame->addRollResult(RollResult::ONE_PIN());
        $this->verifyThat($frame->isComplete(), equalTo(true));

        $frame = $this->createUnitUnderTest();
        $frame->setAsLastFrame();

        $frame->addRollResult(RollResult::ONE_PIN());
        $frame->addRollResult(RollResult::SPARE());
        $this->verifyThat($frame->isComplete(), equalTo(false));
    }

    public function testItShouldRecordItsScoreUpToAMaximumOfThirty()
    {
        $this->uut()->addToScore(RollResult::STRIKE);
        $this->uut()->addToScore(RollResult::STRIKE);
        $this->uut()->addToScore(RollResult::STRIKE);
        $this->uut()->addToScore(RollResult::STRIKE);
        $this->verifyThat($this->uut()->getScore(), equalTo(Frame::MAX_SCORE_PER_FRAME));
    }
}
