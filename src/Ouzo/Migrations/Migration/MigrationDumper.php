<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Symfony\Component\Console\Output\OutputInterface;

class MigrationDumper
{
    private const SCHEMA = 'SCHEMA';
    private const DATA = 'DATA';

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

    public function dumpSchema($file): void
    {
        $this->dump($file, self::SCHEMA);
    }

    public function dumpData($file): void
    {
        $this->dump($file, self::DATA);
    }

    public function dump($file, $type): void
    {
        $this->output->write("<info>Dumping {$type} to {$file}... </info>");

        $user = $this->dbConfig->getUser();
        $host = $this->dbConfig->getHost();
        $port = $this->dbConfig->getPort();
        $dbName = $this->dbConfig->getDbName();
        $options = $type === 'SCHEMA' ? '--schema-only' : '--data-only';

        $command = "pg_dump -t 'public.*' -U {$user} -h {$host} -p {$port} -f {$file} ${options} {$dbName} 2>&1";
        $this->commandExecutor->execute($command);

        $this->output->writeln('<comment>DONE</comment>');
    }
}