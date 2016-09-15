<?php

namespace Blackjack\Ui;

use Blackjack\Card;

class AsciiCardDrawer
{
    const CARD_SEPARATION_SPACE_COUNT = 3;
    const HORIZONTAL_BORDER = '-';
    const VERTICAL_BORDER = '|';
    const PLACEHOLDER_CARD_SUIT = 'x';
    const TILE_CARD_FACE_UP = ' ';
    const TILE_CARD_FACE_DOWN = '#';

    const SUIT_SYMBOL_MAPPING = [
        Card::SUIT_DIAMONDS => '♦',
        Card::SUIT_CLUBS => '♣',
        Card::SUIT_HEARTS => '♥',
        Card::SUIT_SPADES => '♠',
    ];

    private $columnsCount = 13;
    private $rowsCount = 7;

    private $numberOfCardsToBuild;

    /**
     * @var string
     */
    private $drawing;

    /**
     * @var bool
     */
    private $shouldHideFirstCard = false;

    /**
     * @param Card[] $cards
     */
    public function drawCards(array $cards): string
    {
        $this->drawing = '';
        $this->numberOfCardsToBuild = count($cards);

        $this->doDrawing($cards);

        return $this->drawing;
    }

    public function isShouldHideFirstCard(): bool
    {
        return $this->shouldHideFirstCard;
    }

    public function setShouldHideFirstCard(bool $shouldHideFirstCard)
    {
        $this->shouldHideFirstCard = $shouldHideFirstCard;
    }

    /**
     * @param Card[] $cards
     */
    private function doDrawing(array $cards)
    {
        foreach ($cards as $card) {
            $this->addHorizontalBorder();

            if ($card !== end($cards)) {
                $this->addSeparationForNextCard();
            }
        }

        $this->addNewLine();

        for ($rowIndex = 0; $rowIndex < $this->rowsCount; ++$rowIndex) {
            foreach ($cards as $card) {
                $shouldHideCard = $card === reset($cards) && $this->isShouldHideFirstCard();
                $this->addRow($card, $rowIndex, $shouldHideCard);

                if ($card !== end($cards)) {
                    $this->addSeparationForNextCard();
                }
            }
            $this->addNewLine();
        }

        foreach ($cards as $card) {
            $this->addHorizontalBorder();

            if ($card !== end($cards)) {
                $this->addSeparationForNextCard();
            }
        }
    }

    /**
     * @return string
     */
    private function addHorizontalBorder()
    {
        $this->drawing .= str_repeat(static::HORIZONTAL_BORDER, $this->columnsCount);
    }

    private function addRow(Card $card, int $currentRowIndex, bool $hideCard = false)
    {
        $row = static::VERTICAL_BORDER;
        $tile = $hideCard ? static::TILE_CARD_FACE_DOWN : static::TILE_CARD_FACE_UP;
        $row .= str_repeat($tile, $this->columnsCount - 2);

        if ($currentRowIndex === 0 && !$hideCard) {
            if ($card->getRank() === 10) {
                $row{strlen(static::VERTICAL_BORDER) + 1} = '1';
                $row{strlen(static::VERTICAL_BORDER) + 2} = '0';
            } else {
                $row{strlen(static::VERTICAL_BORDER) + 1} = $this->getCorrespondingRankDisplay($card->getRank());
            }
        }

        if ($currentRowIndex === (int) ($this->rowsCount / 2) && !$hideCard) {
            $row{(int) ($this->columnsCount / 2)} = static::PLACEHOLDER_CARD_SUIT;
            $row = $this->replaceSuitPlaceholder($row, $card->getSuit());
        }

        if ($currentRowIndex === $this->rowsCount - 1 && !$hideCard) {
            $charIndex = $this->columnsCount - strlen(static::VERTICAL_BORDER) - 2;
            if ($card->getRank() === 10) {
                $row{$charIndex -1} = '1';
                $row{$charIndex} = '0';
            } else {
                $row{$charIndex} = $this->getCorrespondingRankDisplay($card->getRank());
            }
        }

        $row .= static::VERTICAL_BORDER;

        $this->drawing .= $row;
    }

    private function replaceSuitPlaceholder(string $str, string $suit): string
    {
        return str_replace(
            static::PLACEHOLDER_CARD_SUIT,
            static::SUIT_SYMBOL_MAPPING[$suit],
            $str
        );
    }

    private function getCorrespondingRankDisplay(int $rank): string
    {
        switch ($rank) {
            case Card::RANK_ACE: return 'A';
            case Card::RANK_JACK: return 'J';
            case Card::RANK_QUEEN: return 'Q';
            case Card::RANK_KING: return 'K';
            default: return $rank;
        }
    }

    private function addSeparationForNextCard()
    {
        $this->drawing .= str_repeat(' ', static::CARD_SEPARATION_SPACE_COUNT);
    }

    private function addNewLine()
    {
        $this->drawing .= PHP_EOL;
    }
}
