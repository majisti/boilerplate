<?php

namespace Unit\Bowling;

use Bowling\Frame;
use Bowling\Game;
use Bowling\Roll;
use Bowling\ScoreCalculator;
use Bowling\Scorer;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method Scorer uut()
 * @group scorer
 */
class ScorerTest extends UnitTest
{
    /**
     * @var Game|m\MockInterface
     */
    private $game;

    /**
     * @var Frame|m\MockInterface
     */
    private $frame;

    /**
     * @var ScoreCalculator|m\MockInterface
     */
    private $scoreCalculator;

    public function setUp()
    {
        $this->game = m::spy(Game::class);
        $this->frame = m::spy(Frame::class);
        $this->scoreCalculator = m::spy(ScoreCalculator::class);

        $this->uut()->setScoreCalculator($this->scoreCalculator);

        $this->game->shouldReceive('addRoll')->andReturn($this->frame)->byDefault();
    }
    
    public function createUnitUnderTest()
    {
        return new Scorer($this->game);
    }

    public function testItAddsRollToTheGameAfterScoring()
    {
        $this->game->shouldReceive('addRoll')->once()->andReturn($this->frame)->ordered();
        $this->scoreCalculator->shouldReceive('calculateScore')->once()->ordered();

        $this->uut()->throw(Roll::STRIKE());
    }
}
