<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Exception;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Strings;

class MigrationLoader
{
    private const MIGRATION_FILE_MASK = '/[0-9]{9,}_.+\.php/';

    public function loadMigrations(array $dirs): array
    {
        $versions = Arrays::map(SchemaMigration::all(), Functions::extract()->version);

        $migrations = [];
        foreach ($dirs as $dir) {
            $migrations = array_replace($migrations, $this->loadMigrationsFromDir($dir, $versions));
        }
        ksort($migrations, SORT_STRING | SORT_ASC);
        return $migrations;
    }

    public function loadMigrationsFromDir(string $dir, array $versions): array
    {
        if (empty($dir)) {
            return [];
        }
        if (!file_exists($dir)) {
            throw new Exception("Migration directory `{$dir}` does not exist.");
        }
        $migrations = [];
        $files = scandir($dir, 0);
        for ($i = 2; $i < count($files); $i++) {
            $file = $files[$i];
            if (preg_match('' . self::MIGRATION_FILE_MASK . '', $file)) {
                $path = $dir . '/' . $file;
                $version = substr($file, 0, strpos($file, '_'));
                if (is_file($path) && !in_array($version, $versions)) {
                    include_once($path);
                    $className = Strings::removeSuffix(substr($file, strpos($file, '_') + 1), '.php');
                    $migrations[$version] = $className;
                }
            }
        }
        return $migrations;
    }
}