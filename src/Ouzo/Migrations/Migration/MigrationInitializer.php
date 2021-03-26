<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Ouzo\Db;
use Ouzo\Db\ModelDefinition;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationInitializer
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
        $db = new Db(false);
        $this->output->write("<info>Connecting to db {$this->dbConfig}... </info>");
        $db->connectDb($this->dbConfig->toArray());
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