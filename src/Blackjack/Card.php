<?php

namespace Blackjack;

class Card
{
    const CARDS_PER_TYPE_COUNT = 13;

    const RANK_ACE = 1;
    const RANK_KING = 13;
    const RANK_QUEEN = 12;
    const RANK_JACK = 11;

    const SUIT_DIAMONDS = 'diamonds';
    const SUIT_SPADES = 'spades';
    const SUIT_HEARTS = 'hearts';
    const SUIT_CLUBS = 'clubs';

    const SUITS = [
        self::SUIT_DIAMONDS,
        self::SUIT_SPADES,
        self::SUIT_HEARTS,
        self::SUIT_CLUBS,
    ];

    /**
     * @var string
     */
    private $suit;

    /**
     * @var int
     */
    private $rank;

    public function __construct(string $suit = self::SUIT_SPADES, int $rank = self::RANK_ACE)
    {
        $this->suit = $suit;
        $this->rank = $rank;
    }

    public function getRank(): int
    {
        return $this->rank;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }

    public static function createRandom(): Card
    {
        $types = self::SUITS;

        $randomType = $types[rand(0, count($types) - 1)];
        $randomValue = rand(1, static::CARDS_PER_TYPE_COUNT);

        return new static($randomType, $randomValue);
    }
}
