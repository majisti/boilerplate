<?php

namespace AppBundle\Command;

use Blackjack\DeckBuilder;
use Blackjack\Ui\AsciiCardDrawer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BlackjackCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('majisti:game:blackjack')
            ->setDescription('Play a game of BlackJack against the computer!')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //this is far from complete yet, just playing around with ascii card drawing

        $deckBuilder = new DeckBuilder();
        $deck = $deckBuilder
            ->addAllCards()
            ->shuffle()
            ->getDeck();

        $drawer = new AsciiCardDrawer();

        $drawer->setShouldHideFirstCard(true);
        $output->writeln('<info>DEALER</info>');
        $output->writeln($drawer->drawCards([
            $deck->get(0),
            $deck->get(1),
        ]));

        $drawer->setShouldHideFirstCard(false);
        $output->writeln('<info>PLAYER #1</info>');
        $output->writeln((new AsciiCardDrawer())->drawCards([
            $deck->get(2),
            $deck->get(3),
        ]));
    }
}
