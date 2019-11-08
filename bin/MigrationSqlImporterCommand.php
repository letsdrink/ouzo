<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Command;

use Ouzo\Migration\MigrationDbConfig;
use Ouzo\Migration\MigrationImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationSqlImporterCommand extends Command
{
    public function configure()
    {
        $this->setName('migration:sql_import')
            ->addOption('db_name', 'N', InputOption::VALUE_REQUIRED, 'Database name')
            ->addOption('db_user', 'U', InputOption::VALUE_REQUIRED, 'Database user')
            ->addOption('db_pass', 'S', InputOption::VALUE_REQUIRED, 'Database password')
            ->addOption('db_host', 'H', InputOption::VALUE_REQUIRED, 'Database host')
            ->addOption('db_port', 'P', InputOption::VALUE_REQUIRED, 'Database port')
            ->addArgument('files', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'SQL file to import');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dbConfig = new MigrationDbConfig($input);
        $files = $input->getArgument('files');

        $output->writeln("Database: {$dbConfig}");

        $importer = new MigrationImporter($output, $dbConfig);
        $importer->importAll($files);
    }
}