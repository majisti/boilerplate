<?php

namespace Bowling;

class BowlingFactory
{
    public function createNewGame(): Game
    {
        return new Game();
    }

    public function createGutterGame(): Game
    {
        $game = $this->createNewGame();

        for ($i = 0; $i < Game::MAX_NUMBER_OF_ROLLS_POSSIBLE; ++$i) {
            $game->addRoll(Roll::GUTTER());
        }

        return $game;
    }

    public function createPerfectGame(): Game
    {
        $game = $this->createNewGame();

        for ($i = 0; $i < Game::MAX_NUMBER_OF_STRIKES_POSSIBLE; ++$i) {
            $game->addRoll(Roll::STRIKE());
        }

        return $game;
    }

    /*
     * fixme: I do not think we should have a gutter on the last roll
     * Note: Last roll on last frame will be a gutter.
     */
    public function createSpareGame(Roll $rollWhenNotASpare): Game
    {
        $game = $this->createNewGame();

        for ($i = 0; $i < Game::MAX_NUMBER_OF_FRAMES_POSSIBLE; ++$i) {
            $game->addRoll($rollWhenNotASpare);
            $game->addRoll(Roll::SPARE());
        }

        $game->addRoll($rollWhenNotASpare);

        return $game;
    }

    public function createFrame(): Frame
    {
        return new Frame();
    }
}
