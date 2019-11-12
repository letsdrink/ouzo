<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Symfony\Component\Console\Output\OutputInterface;

class MigrationImporter
{
    /* @var OutputInterface */
    private $output;
    /* @var MigrationDbConfig */
    private $dbConfig;
    /* @var MigrationCommandExecutor */
    private $commandExecutor;

    public function __construct(OutputInterface $output, MigrationDbConfig $dbConfig, MigrationCommandExecutor $commandExecutor)
    {
        $this->output = $output;
        $this->dbConfig = $dbConfig;
        $this->commandExecutor = $commandExecutor;
    }

    public function importAll(array $files)
    {
        foreach ($files as $file) {
            $this->import($file);
        }
    }

    public function import($file): void
    {
        $this->output->write("<info>Importing file {$file}... </info>");

        $user = $this->dbConfig->getUser();
        $host = $this->dbConfig->getHost();
        $port = $this->dbConfig->getPort();
        $dbName = $this->dbConfig->getDbName();

        $command = "psql -e -U {$user} -h {$host} -p {$port} -f {$file} {$dbName} 2>&1";
        $this->commandExecutor->execute($command);

        $this->output->writeln('<comment>DONE</comment>');
    }
}