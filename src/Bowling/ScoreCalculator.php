<?php

namespace Bowling;

/**
 * @author Steven Rosato <steven.rosato@majisti.com>
 */
class ScoreCalculator
{
    const BONUS_SPARE = 1;
    const BONUS_STRIKE = 2;

    const MAX_SCORE_PER_FRAME = 30;

    private $bonusesToApply;

    public function calculateScoreForGame(Game $game): int
    {
        $score = 0;
        $this->bonusesToApply = [];
        $lastRollValue = 0;

        foreach ($game->getFrames() as $frameIndex => $frame) {
            $frameScore = 0;
            foreach ($frame->getRolls() as $rollIndex => $roll) {
                $index = $frameIndex + $rollIndex;
                $value = $this->rollIsSpare($roll)
                    ? RollResult::STRIKE - $lastRollValue
                    : $roll->getValue();

                $lastRollValue = $value;

                //fixme: we are missing the bonus on the first frame!
                if (isset($this->bonusesToApply[$index])) {
                    while ($this->bonusesToApply[$index] > 0) {
                        $frameScore += $value;
                        $this->bonusesToApply[$index]--;
                    }
                }

                if ($this->rollIsStrike($roll)) {
                    $this->applyBonusOnNextRolls($index, self::BONUS_STRIKE);
                } elseif ($this->rollIsSpare($roll)) {
                    $this->applyBonusOnNextRolls($index, self::BONUS_SPARE);
                }

                $frameScore += $value;
            }

            if ($frameScore > self::MAX_SCORE_PER_FRAME) {
                $frameScore = self::MAX_SCORE_PER_FRAME;
            }

            $score += $frameScore;
        }

        return $score;
    }

    private function applyBonusOnNextRolls(int $currentIndex, int $numberOfRollsToApplyBonus)
    {
        for ($i = 1; $i <= $numberOfRollsToApplyBonus; $i++) {
            $index = $currentIndex + $i;
            $this->bonusesToApply[$index] = isset($this->bonusesToApply[$index])
                ? $this->bonusesToApply[$index] + 1
                : 1;
        }
    }

    private function rollIsSpare(RollResult $roll)
    {
        return $roll == RollResult::SPARE();
    }

    private function rollIsStrike(RollResult $roll)
    {
        return $roll == RollResult::STRIKE();
    }
}