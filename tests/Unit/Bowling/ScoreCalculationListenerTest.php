<?php

namespace Unit\Bowling;

use Bowling\Game;
use Bowling\GameEvent;
use Bowling\ScoreCalculationListener;
use Bowling\ScoreCalculator;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method ScoreCalculationListener uut()
 */
class ScoreCalculationListenerTest extends UnitTest
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
        $listener = new ScoreCalculationListener();
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
}
