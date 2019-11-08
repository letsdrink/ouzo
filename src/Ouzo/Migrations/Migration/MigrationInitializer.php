<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Ouzo\Db;
use Ouzo\Db\ModelDefinition;
use Ouzo\Utilities\Objects;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationInitializer
{
    /* @var OutputInterface */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function initMigrations(Db $db): void
    {
        $this->output->write("<info>Initializing migrations... </info>");
        $db->execute("CREATE TABLE schema_migrations(
                id SERIAL PRIMARY KEY,
                version TEXT,
                applied_at TIMESTAMP
            )");
        $this->output->writeln('<comment>DONE</comment>');
    }

    public function dropMigrations(Db $db): void
    {
        $this->output->write("<info>Dropping migrations... </info>");
        $db->execute("DROP TABLE IF EXISTS schema_migrations CASCADE");
        $this->output->writeln('<comment>DONE</comment>');
    }

    public function connectToDatabase(): Db
    {
        $dbConfig = Objects::toString($this->dbConfig);
        $db = new Db(false);
        $this->output->write("<info>Connecting to db {$dbConfig}... </info>");
        $db->connectDb($this->dbConfig);
        SchemaMigration::$db = $db;
        ModelDefinition::resetCache();
        $this->output->writeln('<comment>DONE</comment>');
        return $db;
    }

    public function resetMigrations(): void
    {
        $this->output->write("<info>Removing all migrations... </info>");
        SchemaMigration::where()->deleteAll();
        $this->output->writeln('<comment>DONE</comment>');
    }
}