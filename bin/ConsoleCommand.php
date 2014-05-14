<?php
use Boris\Boris;
use Symfony\Component\Console\Command\Command;

class ConsoleCommand extends Command
{
    public function configure()
    {
        $this->setName('ouzo:console');
    }

    public function execute()
    {
        $boris = new Boris('ouzo> ');
        $boris->start();
    }
}