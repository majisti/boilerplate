<?php

namespace Unit\Bowling;

use ArrayIterator;
use Bowling\Exception\MaximumScoreExceededException;
use Bowling\Frame;
use Bowling\Roll;
use Tests\Unit\UnitTest;

/**
 * @method Frame uut()
 */
class FrameTest extends UnitTest
{
    public function testItShouldHaveAMaximumOfTwoRolls()
    {
        $this->verifyThat($this->uut()->isComplete(), equalTo(false));

        $this->uut()->addRoll(Roll::ONE_PIN());
        $this->verifyThat($this->uut()->isComplete(), equalTo(false));

        $this->uut()->addRoll(Roll::SPARE());
        $this->verifyThat($this->uut()->isComplete(), equalTo(true));
    }

    public function testItShouldBeCompletedWhenAStrikeResultIsAdded()
    {
        $this->uut()->addRoll(Roll::STRIKE());
        $this->uut()->addRoll(Roll::STRIKE());
        $this->verifyThat($this->uut()->isComplete(), equalTo(true));
        $this->verifyThat($this->uut()->rollCount(), equalTo(1));
    }

    public function testLastFrameCanHaveABonusRollsIfTenPinsAreKnockedDownInTheFirstTwoRolls()
    {
        $frame = $this->createUnitUnderTest();
        $frame->setAsLastFrame();

        $frame->addRoll(Roll::ONE_PIN());
        $frame->addRoll(Roll::ONE_PIN());
        $this->verifyThat($frame->isComplete(), equalTo(true));

        $frame = $this->createUnitUnderTest();
        $frame->setAsLastFrame();

        $frame->addRoll(Roll::ONE_PIN());
        $frame->addRoll(Roll::SPARE());
        $this->verifyThat($frame->isComplete(), equalTo(false));
    }

    public function testItShouldThrowExceptionWhenMaximumFrameScoreExceeded()
    {
        $this->expectException(MaximumScoreExceededException::class);
        $this->uut()->setScore(Frame::MAX_SCORE_PER_FRAME + 1);
    }

    public function testItShouldBePossibleToResetScore()
    {
        $this->uut()->setScore(15);
        $this->uut()->resetScore();

        $this->verifyThat($this->uut()->getScore(), equalTo(0));
    }

    public function testCanRetrieveRollsIndependently()
    {
        $this->verifyThat($this->uut()->getRoll(Frame::FIRST_ROLL), is(nullValue()));
        $this->verifyThat($this->uut()->getRoll(Frame::SECOND_ROLL), is(nullValue()));
        $this->verifyThat($this->uut()->getRoll(Frame::THIRD_ROLL), is(nullValue()));

        $this->uut()->addRoll(Roll::ONE_PIN());
        $this->uut()->addRoll(Roll::SPARE());

        $this->uut()->setAsLastFrame();
        $this->uut()->addRoll(Roll::THREE_PINS());

        $this->verifyThat($this->uut()->getRoll(Frame::FIRST_ROLL), equalTo(Roll::ONE_PIN()));
        $this->verifyThat($this->uut()->getRoll(Frame::SECOND_ROLL), equalTo(Roll::SPARE()));
        $this->verifyThat($this->uut()->getRoll(Frame::THIRD_ROLL), equalTo(Roll::THREE_PINS()));
    }

    public function testCanEditARoll()
    {
        $this->uut()->addRoll(Roll::ONE_PIN());
        $this->uut()->addRoll(Roll::THREE_PINS());

        $this->uut()->editRoll(Frame::FIRST_ROLL, Roll::FOUR_PINS());

        $this->verifyThat($this->uut()->getRoll(Frame::FIRST_ROLL), equalTo(Roll::FOUR_PINS()));
    }

    public function testCanRemoveLastRoll()
    {
        $this->uut()->addRoll(Roll::ONE_PIN());
        $this->uut()->addRoll(Roll::THREE_PINS());

        $this->uut()->removeLastRoll();

        $this->verifyThat($this->uut()->getRollsCount(), equalTo(1));
        $this->verifyThat($this->uut()->isComplete(), is(false));
    }

    public function testIsIterable()
    {
        $this->uut()->setAsLastFrame();

        $this->uut()->addRoll(Roll::ONE_PIN());
        $this->uut()->addRoll(Roll::SPARE());
        $this->uut()->addRoll(Roll::THREE_PINS());

        $iterator = $this->uut()->getIterator();
        $this->verifyThat($iterator, is(anInstanceOf(ArrayIterator::class)));
        $this->verifyThat($iterator->count(), is(equalTo(3)));
    }
}
