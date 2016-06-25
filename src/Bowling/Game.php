<?php

namespace Bowling;

class Game
{
    const MAX_NUMBER_OF_ROLLS_POSSIBLE = 21;
    const MAX_NUMBER_OF_STRIKES_POSSIBLE = 12;
    const MAX_NUMBER_OF_FRAMES_POSSIBLE = 10;

    /**
     * @var FrameFactory
     */
    private $frameFactory;

    /**
     * @var Frame[]
     */
    private $frames = [];

    /**
     * @var RollListener[]
     */
    private $rollListeners = [];

    public function roll(RollResult $roll)
    {
        $currentFrame = $this->getCurrentFrame();
        $currentFrame->addRollResult($roll);

        if ($this->getFramesCount() >= self::MAX_NUMBER_OF_FRAMES_POSSIBLE && !$currentFrame->isLastFrame()) {
            $currentFrame->setAsLastFrame();
        }

        $this->notifyRollListeners($roll);

        if ($currentFrame->isComplete()) {
            $this->newFrame();
        }
    }

    /**
     * @return RollResult[]
     */
    public function getRolls()
    {
        $rolls = [];
        foreach ($this->getFrames() as $frame) {
            $rolls = array_merge($rolls, $frame->getRolls());
        }

        return $rolls;
    }

    public function getFrames()
    {
        return $this->frames;
    }

    /**
     * @param int $index
     *
     * @return Frame|null
     */
    public function getFrame(int $index)
    {
        if (array_key_exists($index - 1, $this->frames)) {
            return $this->frames[$index - 1];
        }

        return;
    }

    private function getCurrentFrame(): Frame
    {
        if (empty($this->frames)) {
            $this->newFrame();
        }

        return $this->getLastFrame();
    }

    public function getFrameFactory(): FrameFactory
    {
        if (!$this->frameFactory) {
            $this->frameFactory = new FrameFactory();
        }

        return $this->frameFactory;
    }

    public function setFrameFactory(FrameFactory $frameFactory)
    {
        $this->frameFactory = $frameFactory;
    }

    /**
     * @return Frame|null
     */
    private function getLastFrame()
    {
        if (empty($this->frames)) {
            return;
        }

        return $this->frames[count($this->frames) - 1];
    }

    private function newFrame(): Frame
    {
        $frame = $this->getFrameFactory()->createFrame();

        if ($this->getFramesCount() < self::MAX_NUMBER_OF_FRAMES_POSSIBLE) {
            $this->frames[] = $frame;
        }

        return $this->getLastFrame();
    }

    public function getFramesCount(): int
    {
        return count($this->getFrames());
    }

    public function getCurrentScore(): int
    {
        $score = 0;

        foreach ($this->getFrames() as $frame) {
            $score += $frame->getScore();
        }

        return $score;
    }

    public function addRollListener(RollListener $listener)
    {
        $this->rollListeners[] = $listener;
    }

    /**
     * @return RollListener[]
     */
    public function getRollListeners(): array
    {
        return $this->rollListeners;
    }

    private function notifyRollListeners(RollResult $roll)
    {
        foreach ($this->getRollListeners() as $rollListener) {
            $rollListener->onNewRoll(new RollEvent($this->getCurrentFrame(), $roll));
        }
    }
}
