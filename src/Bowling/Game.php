<?php

namespace Bowling;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use IteratorAggregate;

class Game implements IteratorAggregate
{
    const MAX_NUMBER_OF_ROLLS_POSSIBLE = 21;
    const MAX_NUMBER_OF_STRIKES_POSSIBLE = 12;
    const MAX_NUMBER_OF_FRAMES_POSSIBLE = 10;

    /**
     * @var BowlingFactory
     */
    private $bowlingFactory;

    /**
     * @var Frame[]|ArrayCollection
     */
    private $frames;

    /**
     * @var GameDispatcher
     */
    private $dispatcher;

    public function __construct()
    {
        $this->frames = new ArrayCollection();
    }

    /**
     * @return Frame The frame where the roll was added to
     */
    public function addRoll(Roll $roll): Frame
    {
        $frame = $this->getCurrentFrame();
        $frame->addRoll($roll);

        $this->notifyNewRoll($frame, $roll);

        if ($this->getFramesCount() >= self::MAX_NUMBER_OF_FRAMES_POSSIBLE && !$frame->isLastFrame()) {
            $frame->setAsLastFrame();
        }

        $this->addNewFrameWhenPossible();

        return $frame;
    }

    /**
     * @return Roll[]
     */
    public function getRolls()
    {
        $rolls = [];
        foreach ($this->getFrames() as $frame) {
            $rolls = array_merge($rolls, $frame->getRolls());
        }

        return $rolls;
    }

    public function getRollsCount(): int
    {
        return count($this->getRolls());
    }

    /**
     * @return Frame[]
     */
    public function getFrames()
    {
        return $this->frames->toArray();
    }

    /**
     * @param int $index
     *
     * @return Frame|null
     */
    public function getFrame(int $index)
    {
        if ($this->frames->containsKey($index - 1)) {
            return $this->frames->get($index - 1);
        }

        return null;
    }

    public function getFrameIndex(Frame $frameToFind): int
    {
        foreach ($this->getFrames() as $index => $frame) {
            if ($frame === $frameToFind) {
                return $index + 1;
            }
        }

        return -1;
    }

    public function getRoll(int $index)
    {
        if (array_key_exists($index - 1, $this->getRolls())) {
            return $this->getRolls()[$index - 1];
        }

        return null;
    }

    public function rollIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getRolls());
    }

    public function hasRolls(): bool
    {
        return $this->getRollsCount() > 0;
    }

    public function hasFrames(): bool
    {
        return $this->getFramesCount() > 0;
    }

    public function isOnLastFrame(): bool
    {
        return $this->getCurrentFrame()->isLastFrame();
    }

    public function setGameDispatcher(GameDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return Frame|null
     */
    public function getFirstFrame()
    {
        if ($this->frames->isEmpty()) {
            return null;
        }

        return $this->frames->first();
    }

    private function getCurrentFrame(): Frame
    {
        if ($this->frames->isEmpty()) {
            $this->advanceFrame();
        }

        return $this->getLastFrame();
    }

    public function getBowlingFactory(): BowlingFactory
    {
        if (!$this->bowlingFactory) {
            $this->bowlingFactory = new BowlingFactory();
        }

        return $this->bowlingFactory;
    }

    public function setBowlingFactory(BowlingFactory $bowlingFactory)
    {
        $this->bowlingFactory = $bowlingFactory;
    }

    /**
     * @return Frame|null
     */
    public function getLastFrame()
    {
        if ($this->frames->isEmpty()) {
            return null;
        }

        return $this->frames->last();
    }

    /**
     * Uncompleted frames gets completed with a spare.
     */
    public function advanceFrame(): Frame
    {
        $this->completeFrameWithSpare();

        $frame = $this->getBowlingFactory()->createFrame();

        if ($this->getFramesCount() < self::MAX_NUMBER_OF_FRAMES_POSSIBLE) {
            $this->frames->add($frame);
        }
        
        $this->notifyNewFrame($frame);

        return $frame;
    }

    public function getFramesCount(): int
    {
        return $this->frames->count();
    }

    public function getCurrentScore(): int
    {
        $score = 0;
        foreach ($this->getFrames() as $frame) {
            $score += $frame->getScore();
        }

        return $score;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getFrames());
    }

    private function addNewFrameWhenPossible()
    {
        if ($this->getCurrentFrame()->isComplete() &&
            $this->getFramesCount() <= static::MAX_NUMBER_OF_FRAMES_POSSIBLE) {
            $this->advanceFrame();
        }
    }

    private function notifyNewRoll(Frame $frame, Roll $roll)
    {
        if ($this->dispatcher) {
            $this->dispatcher->notifyNewRoll($this, $frame, $roll);
        }
    }
    
    private function notifyNewFrame(Frame $frame)
    {
        if ($this->dispatcher) {
            $this->dispatcher->notifyNewFrame($this, $frame);
        }
    }

    public function completeFrameWithSpare()
    {
        $lastFrame = $this->getLastFrame();

        if ($lastFrame && $lastFrame->getRollsCount() === 1) {
            $lastFrame->addRoll(Roll::SPARE());
        }
    }
}
