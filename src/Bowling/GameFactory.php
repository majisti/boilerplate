<?php

namespace Bowling;

class GameFactory
{
    public function createNewGame(): Game
    {
        return new Game();
    }

    public function createGutterGame(): Game
    {
        $game = $this->createNewGame();

        for ($i = 0; $i < Game::MAX_NUMBER_OF_ROLLS_POSSIBLE; ++$i) {
            $game->roll(RollResult::GUTTER());
        }

        return $game;
    }

    public function createPerfectGame(): Game
    {
        $game = $this->createNewGame();

        for ($i = 0; $i < Game::MAX_NUMBER_OF_STRIKES_POSSIBLE; ++$i) {
            $game->roll(RollResult::STRIKE());
        }

        return $game;
    }

    /*
     * Note: Last roll on last frame will be a gutter.
     */
    public function createSpareGame(RollResult $rollWhenNotASpare): Game
    {
        $game = $this->createNewGame();

        for ($i = 0; $i < Game::MAX_NUMBER_OF_FRAMES_POSSIBLE; ++$i) {
            $game->roll($rollWhenNotASpare);
            $game->roll(RollResult::SPARE());
        }

        $game->roll($rollWhenNotASpare);

        return $game;
    }
}
