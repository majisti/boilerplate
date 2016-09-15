<?php

namespace Unit\Blackjack\Ui;

use Blackjack\Card;
use Blackjack\Ui\AsciiCardDrawer;
use Mockery as m;
use Tests\Unit\UnitTest;

/**
 * @method AsciiCardDrawer uut()
 */
class AsciiCardDrawerTest extends UnitTest
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function createUnitUnderTest()
    {
        return new AsciiCardDrawer();
    }

    public function getCards()
    {
        $drawing = <<<EOF
-------------
| 2         |
|           |
|           |
|     ♣     |
|           |
|           |
|         2 |
-------------
EOF;
        yield [[new Card(2, Card::SUIT_CLUBS)], $drawing];

        $drawing = <<<EOF
-------------
| J         |
|           |
|           |
|     ♥     |
|           |
|           |
|         J |
-------------
EOF;
        yield [[new Card(Card::RANK_JACK, Card::SUIT_HEARTS)], $drawing];

        $drawing = <<<EOF
-------------
| Q         |
|           |
|           |
|     ♠     |
|           |
|           |
|         Q |
-------------
EOF;
        yield [[new Card(Card::RANK_QUEEN, Card::SUIT_SPADES)], $drawing];

        $drawing = <<<EOF
-------------
| K         |
|           |
|           |
|     ♦     |
|           |
|           |
|         K |
-------------
EOF;
        yield [[new Card(Card::RANK_KING, Card::SUIT_DIAMONDS)], $drawing];

        $drawing = <<<EOF
-------------
| A         |
|           |
|           |
|     ♦     |
|           |
|           |
|         A |
-------------
EOF;
        yield [[new Card(Card::RANK_ACE, Card::SUIT_DIAMONDS)], $drawing];

        $drawing = <<<EOF
-------------   -------------
| A         |   | 10        |
|           |   |           |
|           |   |           |
|     ♦     |   |     ♦     |
|           |   |           |
|           |   |           |
|         A |   |        10 |
-------------   -------------
EOF;
        yield [
            [
                new Card(Card::RANK_ACE, Card::SUIT_DIAMONDS),
                new Card(10, Card::SUIT_DIAMONDS),
            ],
            $drawing
        ];

        $drawing = <<<EOF
-------------   -------------
|###########|   | K         |
|###########|   |           |
|###########|   |           |
|###########|   |     ♦     |
|###########|   |           |
|###########|   |           |
|###########|   |         K |
-------------   -------------
EOF;
        yield [
            [
                new Card(Card::RANK_ACE, Card::SUIT_DIAMONDS),
                new Card(Card::RANK_KING, Card::SUIT_DIAMONDS),
            ],
            $drawing,
            $shouldHideFirstCard = true
        ];
    }

    /**
     * @param Card[] $card
     * @dataProvider getCards()
     */
    public function testCanDrawAnAsciiRepresentationOfACard(array $cards, string $expectedDrawing, 
        bool $shouldHideFirstCard = false)
    {
        $this->uut()->setShouldHideFirstCard($shouldHideFirstCard);
        $drawing = $this->uut()->drawCards($cards);
        $this->verifyAsciiCard($expectedDrawing, $drawing);
    }

    private function verifyAsciiCard(string $expectedDrawing, string $drawing)
    {
        $this->verifyThat(
            sprintf("\n%s \nwas expected, but \n%s \nwas given.",
                $expectedDrawing, $drawing),
            $drawing, equalTo($expectedDrawing)
        );
    }
}
