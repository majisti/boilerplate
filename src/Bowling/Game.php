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

    public function roll(RollResult $roll)
    {
        $currentFrame = $this->getCurrentFrame();
        $currentFrame->addRollResult($roll);

        if ($this->getFramesCount() >= self::MAX_NUMBER_OF_FRAMES_POSSIBLE && !$currentFrame->isLastFrame()) {
            $currentFrame->setAsLastFrame();
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

    private function getCurrentFrame()
    {
        if (empty($this->frames)) {
            $this->addFrame();
        }

        $currentFrame = $this->getLastFrame();

        if ($currentFrame->isComplete()) {
            $currentFrame = $this->addFrame();
        }

        return $currentFrame;
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
            return null;
        }

        return $this->frames[count($this->frames) - 1];
    }

    private function addFrame(): Frame
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
}
