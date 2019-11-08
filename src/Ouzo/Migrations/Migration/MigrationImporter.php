<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Exception;
use Ouzo\Utilities\Objects;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationImporter
{
    /* @var OutputInterface */
    private $output;
    /* @var MigrationDbConfig */
    private $dbConfig;

    public function __construct(OutputInterface $output, MigrationDbConfig $dbConfig)
    {
        $this->output = $output;
        $this->dbConfig = $dbConfig;
    }

    public function import($file): void
    {
        $this->output->write("<info>Importing file {$file}... </info>");
        $command = "psql -e -U {$this->dbConfig->getUser()} -h {$this->dbConfig->getHost()} -p {$this->dbConfig->getPort()} -f {$file} {$this->dbConfig->getDbName()} 2>&1";
        exec($command, $output, $returnStatus);

        if ($returnStatus) {
            $this->output->writeln('<error>ERROR</error>');
            $reason = Objects::toString(array_slice($output, -3));
            throw new Exception("Error executing command {$command}\n\nReason: {$reason}\n\n");
        }

        $this->output->writeln('<comment>DONE</comment>');
    }

    public function importAll(array $files)
    {
        foreach ($files as $file) {
            $this->import($file);
        }
    }
}