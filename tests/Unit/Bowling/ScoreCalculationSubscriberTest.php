<?php

namespace Unit\Bowling;

use Bowling\Game;
use Bowling\GameEvent;
use Bowling\ScoreCalculationSubscriber;
use Bowling\ScoreCalculator;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method ScoreCalculationSubscriber uut()
 */
class ScoreCalculationSubscriberTest extends UnitTest
{
    /**
     * @var ScoreCalculator|m\MockInterface
     */
    private $scoreCalculator;

    public function setUp()
    {
        $this->scoreCalculator = m::mock(ScoreCalculator::class);
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        $listener = new ScoreCalculationSubscriber();
        $listener->setScoreCalculator($this->scoreCalculator);

        return $listener;
    }
    
    public function testUsesScoreCalculatorOnEveryRoll()
    {
        $game = new Game();
        $event = new GameEvent($game);
        
        $this->scoreCalculator->shouldReceive('calculateScore')->once()->with($game);
        $this->uut()->onNewRoll($event);
    }

    public function testIsSubscribedToNewRollEvents()
    {
        $events = $this->uut()->getSubscribedEvents();

        $this->verifyThat($events, self::arrayHasKey(GameEvent::EVENT_NEW_ROLL));
    }
}
