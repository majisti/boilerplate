<?php

namespace Bowling;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
class ScoreListener implements RollListener
{
    const BONUS_ROLLS_FOR_SPARE = 1;
    const BONUS_ROLLS_FOR_STRIKE = 2;

    /**
     * @var BonusCounter[]|ArrayCollection
     */
    private $registeredBonusCounters;

    /**
     * @var RollResult
     */
    private $lastRoll = null;

    public function __construct()
    {
        $this->registeredBonusCounters = new ArrayCollection();
    }

    public function onNewRoll(RollEvent $event)
    {
        $frame = $event->getFrame();
        $roll = $event->getRollResult();

        $rollValue = $roll->isSpare() && $this->lastRoll
            ? RollResult::STRIKE - $this->lastRoll->getValue()
            : $roll->getValue();

        foreach ($this->registeredBonusCounters as $key => $bonusCounter) {
            $bonusCounter->getFrame()->addToScore($rollValue);
            $bonusCounter->decrement();

            if (!$bonusCounter->hasBonusRolls()) {
                $this->registeredBonusCounters->remove($key);
            }
        }

        if (!$frame->isLastFrame()) {
            if ($roll->isStrike()) {
                $this->registeredBonusCounters->add(new BonusCounter($frame, self::BONUS_ROLLS_FOR_STRIKE));
            } elseif ($roll->isSpare()) {
                $this->registeredBonusCounters->add(new BonusCounter($frame, self::BONUS_ROLLS_FOR_SPARE));
            }
        }

        $frame->addToScore($rollValue);

        $this->lastRoll = $roll;
    }
}
