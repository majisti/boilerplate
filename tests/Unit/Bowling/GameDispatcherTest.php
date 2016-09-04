<?php

namespace Unit\Bowling;

use Bowling\Frame;
use Bowling\Game;
use Bowling\GameDispatcher;
use Bowling\GameEvent;
use Bowling\GameListener;
use Bowling\Roll;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method GameDispatcher uut()
 */
class GameDispatcherTest extends UnitTest
{
    protected $uut;

    public function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new GameDispatcher();
    }

    public function testItShouldNotifyGameListenersAboutNewRolls()
    {
        $listeners = [m::spy(GameListener::class), m::spy(GameListener::class)];

        foreach ($listeners as $listener) {
            $listener->shouldReceive('onNewRoll')->once()->with(anInstanceOf(GameEvent::class));
            $this->uut()->addGameListener($listener);
        }

        $this->uut()->notifyNewRoll(new Game(), new Frame(), Roll::GUTTER());
    }

    public function testItShouldNotifyGameListenersAboutNewFrames()
    {
        $listeners = [m::spy(GameListener::class), m::spy(GameListener::class)];
        
        foreach ($listeners as $listener) {
            $listener->shouldReceive('onNewFrame')->once()->with(anInstanceOf(GameEvent::class));
            $this->uut()->addGameListener($listener);
        }
        
        $this->uut()->notifyNewFrame(new Game(), new Frame());
    }
}
