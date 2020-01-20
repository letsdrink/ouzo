<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Ouzo\Db;
use PDO;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationImporter
{
    /* @var OutputInterface */
    private $output;
    /* @var Db */
    private $db;

    public function __construct(OutputInterface $output, Db $db)
    {
        $this->output = $output;
        $this->db = $db;
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

        $body = file_get_contents($file);
        $this->execute($body);

        $this->output->writeln('<comment>DONE</comment>');
    }

    private function execute(string $body): void
    {
        $this->db->dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->dbHandle->exec($body);
    }
}