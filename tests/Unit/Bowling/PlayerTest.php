<?php

namespace Unit\Bowling;

use Bowling\Frame;
use Bowling\Game;
use Bowling\Roll;
use Bowling\ScoreCalculator;
use Bowling\Player;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Player uut()
 */
class PlayerTest extends UnitTest
{
    /**
     * @var Game|m\MockInterface
     */
    private $game;

    /**
     * @var Frame|m\MockInterface
     */
    private $frame;

    public function setUp()
    {
        $this->game = m::spy(Game::class);
        $this->frame = m::spy(Frame::class);

        $this->game->shouldReceive('addRoll')->andReturn($this->frame)->byDefault();
    }
    
    public function createUnitUnderTest()
    {
        return new Player($this->game);
    }

    public function testItAddsRollToTheGameAfterScoring()
    {
        $this->game->shouldReceive('addRoll')->once()->andReturn($this->frame);
        $this->uut()->throw(Roll::STRIKE());
    }
}
