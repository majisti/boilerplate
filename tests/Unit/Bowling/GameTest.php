<?php

namespace Unit\Bowling;

use Bowling\BowlingFactory;
use Bowling\Frame;
use Bowling\Game;
use Bowling\GameEvent;
use Bowling\Roll;
use Mockery as m;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tests\Unit\UnitTest;

/**
 * @method Game uut()
 */
class GameTest extends UnitTest
{
    /**
     * @var Frame|m\MockInterface
     */
    private $frame;

    /**
     * @var BowlingFactory|m\MockInterface
     */
    private $bowlingFactory;

    /**
     * @var EventDispatcher|m\MockInterface
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = m::spy(EventDispatcher::class);
        $this->frame = m::spy(Frame::class);
        $this->bowlingFactory = m::spy(BowlingFactory::class);

        $this->bowlingFactory
            ->shouldReceive('createFrame')
            ->andReturn($this->frame)
            ->byDefault();
        $this->uut()->setBowlingFactory($this->bowlingFactory);

        parent::setUp();
    }

    public function testItCreatesANewFrameWhenItIsCompleted()
    {
        $this->frame->shouldReceive('isComplete')->andReturn(false, false, true);
        $this->addRolls([Roll::ONE_PIN(), Roll::ONE_PIN(), Roll::ONE_PIN()]);

        $this->verifyThat(count($this->uut()->getFrames()), equalTo(2));
    }

    public function testItReturnsAllRolls()
    {
        $this->frame->shouldReceive('getRolls')->andReturn([Roll::GUTTER()]);
        $this->frame->shouldReceive('isComplete')->andReturn(false, true);

        $rollCount = 10;
        for ($i = 0; $i < $rollCount; $i++) {
            $this->uut()->addRoll(Roll::GUTTER());
        }

        $this->verifyThat(count($this->uut()->getRolls()), equalTo($rollCount));
    }

    public function testShouldHaveAMaximumOfTenFrames()
    {
        $this->frame->shouldReceive('isComplete')->andReturn(false, true, true, true, true, true, true, true, true, true, false);

        for ($i = 0; $i < Game::MAX_NUMBER_OF_STRIKES_POSSIBLE + 20; $i++) {
            $this->addRolls([Roll::STRIKE()]);
        }

        $this->verifyThat($this->uut()->getFramesCount(), equalTo(Game::MAX_NUMBER_OF_FRAMES_POSSIBLE));
    }

    public function testItProvidesNthFrame()
    {
        $unwantedFrame = m::spy(Frame::class);
        $unwantedFrame->shouldReceive('isComplete')->andReturn(true);

        $expectedFrame = m::spy(Frame::class);
        $expectedFrame->shouldReceive('isComplete')->andReturn(true);

        $this->bowlingFactory->shouldReceive('createFrame')->andReturn($unwantedFrame, $expectedFrame);

        for ($i = 0; $i < Game::MAX_NUMBER_OF_STRIKES_POSSIBLE; $i++) {
            $this->addRolls([Roll::STRIKE()]);
        }

        $this->verifyThat($this->uut()->getFrame(2), equalTo($expectedFrame));
    }

    public function testShouldReturnFirstFrame()
    {
        $this->addRolls([Roll::STRIKE(), Roll::STRIKE()]);

        $frame = $this->uut()->getFirstFrame();
        $frames = $this->uut()->getFrames();

        $this->verifyThat($frame, is(sameInstance($frames[0])));
    }

    public function testShouldReturnLastFrame()
    {
        $this->addRolls([Roll::STRIKE(), Roll::STRIKE()]);

        $frame = $this->uut()->getLastFrame();
        $frames = $this->uut()->getFrames();

        $this->verifyThat($frame, is(sameInstance($frames[count($frames) - 1])));
    }

    public function testShouldReturnSumOfAllFramesScore()
    {
        $this->frame->shouldReceive('isComplete')->andReturn(false, true);
        $this->frame->shouldReceive('getScore')->atLeast()->times(2)->andReturn(10);
        $this->addRolls([Roll::STRIKE(), Roll::STRIKE()]);

        $this->verifyThat($this->uut()->getCurrentScore(), equalTo(20));
    }

    public function testCanRetrieveIndexOfFrame()
    {
        $this->addRolls([Roll::STRIKE()]);

        $frame = $this->uut()->getFrame($expectedIndex = 1);
        $this->verifyThat($this->uut()->getFrameIndex($frame), equalTo($expectedIndex));
    }
    
    public function testAdvancingFrameShouldCompleteWithSpareWhenFrameHasOneRoll()
    {
        $this->frame->shouldDeferMissing();

        $this->addRolls([Roll::EIGHT_PINS()]);
        $frame = $this->uut()->advanceFrame();
        
        $this->verifyThat($frame->isComplete(), is(true));
        $this->verifyThat($frame->getRollsCount(), equalTo(2));
        $this->verifyThat($frame->getRoll(Frame::SECOND_ROLL), equalTo(Roll::SPARE()));
    }

    public function testIsIterable()
    {
        $this->frame->shouldReceive('isComplete')->andReturn(true);
        $this->addRolls([Roll::STRIKE(), Roll::STRIKE()]);

        $iterator = $this->uut()->getIterator();
        $this->verifyThat($iterator->count(), is(equalTo(3)));
    }

    public function testDispatchesNewRollEvents()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(GameEvent::EVENT_NEW_ROLL, anInstanceOf(GameEvent::class))
        ;
        $this->uut()->setEventDispatcher($this->dispatcher);

        $this->addRolls([Roll::GUTTER()]);
    }

    public function testDispatchesNewFrameEvents()
    {
        $this->dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(GameEvent::EVENT_NEW_FRAME, anInstanceOf(GameEvent::class))
        ;
        $this->uut()->setEventDispatcher($this->dispatcher);

        $this->addRolls([Roll::STRIKE()]);
    }

    /**
     * @param Roll[] $rolls
     */
    private function addRolls(array $rolls)
    {
        foreach ($rolls as $roll) {
            $this->uut()->addRoll($roll);
        }
    }
}
