<?php

namespace Bowling;

class Scorer
{
    /**
     * @var Game
     */
    private $game;

    /**
     * @var ScoreCalculator
     */
    private $scoreCalculator;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function throw(Roll $roll)
    {
        $this->game->addRoll($roll);
        $this->getScoreCalculator()->calculateScore($this->game);
    }

    public function getScoreCalculator(): ScoreCalculator
    {
        if( null === $this->scoreCalculator ) {
            $this->scoreCalculator = new ScoreCalculator();
        }

        return $this->scoreCalculator;
    }

    public function setScoreCalculator(ScoreCalculator $scoreCalculator)
    {
        $this->scoreCalculator = $scoreCalculator;
    }
}
