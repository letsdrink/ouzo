<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Exception;
use Ouzo\Db;
use Ouzo\Utilities\Clock;

class MigrationRunner
{
    public function runAll(Db $db, MigrationProgressBar $progressBar, array $migrations): void
    {
        foreach ($migrations as $version => $className) {
            $progressBar->displayMessage("[$version] $className");
            try {
                $db->runInTransaction(function () use ($className, $version, $db) {
                    $this->runSingleMigration($db, $className, $version);
                });
            } catch (Exception $ex) {
                throw new MigrationFailedException($ex, $className, $version);
            }
            $progressBar->advance();
        }

        $progressBar->finish();
    }

    private function runSingleMigration(Db $db, $className, $version): void
    {
        /** @var Migration $migration */
        $migration = new $className;
        $migration->run($db);
        SchemaMigration::create(['version' => $version, 'applied_at' => Clock::nowAsString()]);
    }
}