<?php

namespace Bowling;

class GameDispatcher
{
    protected $gameListeners = [];

    public function addGameListener(GameListener $listener)
    {
        $this->gameListeners[] = $listener;
    }

    /**
     * @return GameListener[]
     */
    public function getGameListeners(): array
    {
        return $this->gameListeners;
    }

    public function notifyNewRoll(Game $game, Frame $frame, Roll $roll)
    {
        foreach ($this->getGameListeners() as $gameListener) {
            $gameListener->onNewRoll(new GameEvent($game, $frame, $roll));
        }
    }

    public function notifyNewFrame(Game $game, Frame $frame)
    {
        foreach ($this->getGameListeners() as $gameListener) {
            $gameListener->onNewFrame(new GameEvent($game, $frame));
        }
    }
}
