<?php
use Boris\Boris;
use Ouzo\Shell;

class ConsoleShell extends Shell
{
    public function main()
    {
        $boris = new Boris('ouzo> ');
        $boris->start();
    }
}