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
    public function setUp()
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
        yield [[new Card(Card::SUIT_CLUBS, 2)], $drawing];

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
        yield [[new Card(Card::SUIT_HEARTS, Card::RANK_JACK)], $drawing];

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
        yield [[new Card(Card::SUIT_SPADES, Card::RANK_QUEEN)], $drawing];

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
        yield [[new Card(Card::SUIT_DIAMONDS, Card::RANK_KING)], $drawing];

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
        yield [[new Card(Card::SUIT_DIAMONDS, Card::RANK_ACE)], $drawing];

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
                new Card(Card::SUIT_DIAMONDS, Card::RANK_ACE),
                new Card(Card::SUIT_DIAMONDS, 10),
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
                new Card(Card::SUIT_DIAMONDS, Card::RANK_ACE),
                new Card(Card::SUIT_DIAMONDS, Card::RANK_KING),
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
