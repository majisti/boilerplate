<?php

namespace Bowling;

use Doctrine\Common\Collections\ArrayCollection;

class ScoreCalculator
{
    /**
     * @var BonusCounter[]|ArrayCollection
     */
    private $registeredBonusCounters;

    /**
     * @var Roll
     */
    private $lastRoll = null;

    public function calculateScore(Game $game)
    {
        $this->registeredBonusCounters = new ArrayCollection();
        $this->lastRoll = null;

        foreach ($game->getFrames() as $frame) {
            $this->doCalculation($frame);
        }
    }

    private function doCalculation(Frame $frame)
    {
        $frame->resetScore();

        foreach ($frame->getRolls() as $roll) {
            $pinsDowned = $this->calculatePinsDownCount($roll);

            $this->applyBonuses($pinsDowned);
            $this->addToCurrentFrameScore($frame, $pinsDowned);

            if (!$frame->isLastFrame()) {
                $this->registerBonuses($roll, $frame);
            }

            $this->lastRoll = $roll;
        }
    }

    private function addToCurrentFrameScore(Frame $frame, int $valueToAdd)
    {
        $frameScore = $frame->getScore();

        if ($frameScore + $valueToAdd > Frame::MAX_SCORE_PER_FRAME) {
            $frameScore = Frame::MAX_SCORE_PER_FRAME;
        } else {
            $frameScore += $valueToAdd;
        }

        $frame->setScore($frameScore);
    }

    private function registerBonuses(Roll $roll, Frame $frame)
    {
        if ($roll->isStrike()) {
            $this->registeredBonusCounters->add(new BonusCounter($frame, BonusCounter::BONUS_ROLLS_FOR_STRIKE));
        } elseif ($roll->isSpare()) {
            $this->registeredBonusCounters->add(new BonusCounter($frame, BonusCounter::BONUS_ROLLS_FOR_SPARE));
        }
    }

    private function applyBonuses(int $rollValue)
    {
        foreach ($this->registeredBonusCounters as $key => $bonusCounter) {
            $this->addToCurrentFrameScore($bonusCounter->getFrame(), $rollValue);
            $bonusCounter->decrement();

            if (!$bonusCounter->hasBonusRolls()) {
                $this->registeredBonusCounters->remove($key);
            }
        }
    }

    private function calculatePinsDownCount(Roll $roll)
    {
        $rollValue = $roll->isSpare() && $this->lastRoll
            ? Roll::STRIKE - $this->lastRoll->getValue()
            : $roll->getValue();

        return $rollValue;
    }
}
