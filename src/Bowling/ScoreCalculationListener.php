<?php

namespace Bowling;

class ScoreCalculationListener implements GameListener
{
    /**
     * @var ScoreCalculator
     */
    private $scoreCalculator;

    public function onNewRoll(GameEvent $event)
    {
        $this->getScoreCalculator()->calculateScore($event->getGame());
    }

    public function onNewFrame(GameEvent $event)
    {
        //we do not need to do anything here
    }

    public function getScoreCalculator(): ScoreCalculator
    {
        if (null === $this->scoreCalculator) {
            $this->scoreCalculator = new ScoreCalculator();
        }

        return $this->scoreCalculator;
    }

    public function setScoreCalculator(ScoreCalculator $scoreCalculator)
    {
        $this->scoreCalculator = $scoreCalculator;
    }
}
