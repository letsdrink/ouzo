<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Command;

use Ouzo\Migration\MigrationCommand;
use Ouzo\Migration\MigrationDbConfig;
use Ouzo\Migration\MigrationImporter;
use Ouzo\Migration\MigrationInitializer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationSqlImporterCommand extends MigrationCommand
{
    public function configureCommand()
    {
        $this->setName('migration:sql_import')
            ->addArgument('files', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'SQL file to import');
    }

    public function executeCommand(InputInterface $input, OutputInterface $output)
    {
        $dbConfig = new MigrationDbConfig($input);
        $files = $input->getArgument('files');

        $output->writeln("Database: {$dbConfig}");

        $initializer = new MigrationInitializer($output, $dbConfig);
        $db = $initializer->connectToDatabase();
        $importer = new MigrationImporter($output, $db);
        $importer->importAll($files);

        return 0;
    }
}