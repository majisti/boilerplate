<?php

namespace Blackjack;

class Game
{
    /**
     * @var Dealer
     */
    private $dealer;

    /**
     * @var Player
     */
    private $player;

    /**
     * @var Player
     */
    private $winner;

    /**
     * @var bool
     */
    private $isDraw = false;

    public function dealerWins()
    {
        $this->winner = $this->getDealer();
    }

    public function getDealer(): Dealer
    {
        return $this->dealer;
    }

    public function setDealer(Dealer $dealer)
    {
        $this->dealer = $dealer;
    }

    public function getLoser(): Player
    {
        return $this->winner === $this->getDealer()
            ? $this->getDealer()
            : $this->getPlayer();
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player)
    {
        $this->player = $player;
    }

    public function getWinner(): Player
    {
        return $this->winner;
    }

    public function hasDealerWon(): bool
    {
        return $this->hasWon($this->getDealer());
    }

    /**
     * @return bool
     */
    protected function hasWon(Player $player)
    {
        return $this->winner
            ? $this->winner === $player
            : false;
    }

    public function hasLoser(): bool
    {
        return $this->hasWinner();
    }

    public function hasWinner(): bool
    {
        return null !== $this->winner;
    }

    public function hasPlayerWon(): bool
    {
        return $this->hasWon($this->getPlayer());
    }

    public function isDraw(): bool
    {
        return $this->isDraw;
    }

    public function playerWins()
    {
        $this->winner = $this->getPlayer();
    }

    public function setIsDraw()
    {
        $this->isDraw = true;
    }

    public function getPlayerBestScore()
    {
        return $this->getPlayer()->getBestScore();
    }

    public function getDealerBestScore()
    {
        return $this->getDealer()->getBestScore();
    }
}
