<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    public function configure()
    {
        $this->setName('ouzo:test');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Hello world");
    }
}
