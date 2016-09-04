<?php

namespace Bowling;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ScoreCalculationSubscriber implements EventSubscriberInterface
{
    /**
     * @var ScoreCalculator
     */
    private $scoreCalculator;

    public function onNewRoll(GameEvent $event)
    {
        $this->getScoreCalculator()->calculateScore($event->getGame());
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

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            GameEvent::EVENT_NEW_ROLL => 'onNewRoll',
        ];
    }
}
