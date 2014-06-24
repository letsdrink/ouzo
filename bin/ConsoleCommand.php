<?php
use Boris\Boris;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleCommand extends Command
{
    public function configure()
    {
        $this->setName('ouzo:console');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $boris = new Boris('ouzo> ');
        $boris->start();
    }
}