<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Exception;
use Ouzo\Utilities\Objects;

class MigrationCommandExecutor
{
    public function execute($command): void
    {
        exec($command, $output, $returnStatus);

        if ($returnStatus) {
            $reason = Objects::toString(array_slice($output, -3));
            throw new Exception("Error executing command {$command}\n\nReason: {$reason}\n\n");
        }
    }
}