<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    public function configure(): void
    {
        $this->setName('ouzo:test');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln("Hello world");
    }
}
