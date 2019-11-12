<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Command;

use Ouzo\Migration\MigrationCommandExecutor;
use Ouzo\Migration\MigrationCommandHelper;
use Ouzo\Migration\MigrationDbConfig;
use Ouzo\Migration\MigrationDumper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationDumperCommand extends Command
{
    public function configure()
    {
        MigrationCommandHelper::addDbOptions($this)
            ->setName('migration:dump')
            ->addArgument('schema_file', InputArgument::REQUIRED, 'Schema output file')
            ->addArgument('data_file', InputArgument::REQUIRED, 'Data output file');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dbConfig = new MigrationDbConfig($input);
        $schemaFile = $input->getArgument('schema_file');
        $dataFile = $input->getArgument('data_file');

        $output->writeln("Database: {$dbConfig}");

        $dumper = new MigrationDumper($output, $dbConfig, new MigrationCommandExecutor());
        $dumper->dumpSchema($schemaFile);
        $dumper->dumpData($dataFile);
    }
}