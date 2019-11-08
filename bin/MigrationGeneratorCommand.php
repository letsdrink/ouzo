<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Command;


use Ouzo\Utilities\Clock;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationGeneratorCommand extends Command
{
    /* @var InputInterface */
    private $input;
    /* @var OutputInterface */
    private $output;

    public function configure()
    {
        $this->setName('migration:generate')
            ->addArgument('name', InputArgument::REQUIRED, 'Migration name')
            ->addArgument('dir', InputArgument::OPTIONAL, 'Migration directory');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $name = $this->input->getArgument('name');
        $dir = $this->input->getArgument('dir');
        $dir = $dir ? $dir . '/' : '';
        $clock = Clock::now();
        $date = $clock->format('YmdHis');
        $path = "{$dir}{$date}_{$name}.php";

        $this->output->writeln("Migration file name: <info>{$path}</info>");

        $data = <<<MIGRATION
<?php

use Ouzo\Db;
use Ouzo\Migration;

class {$name} extends Migration
{

    public function run(Db \$db)
    {
        \$db->execute("SELECT 1");
    }
}        
MIGRATION;

        file_put_contents($path, $data);

        $this->output->writeln("<comment>Generating...</comment> <info>DONE</info>");
    }
}