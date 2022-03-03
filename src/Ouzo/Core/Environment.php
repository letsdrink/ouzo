<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Logger\Logger;
use RuntimeException;

class Environment
{
    public function __construct()
    {
    }

    public function init(): void
    {
        $env = getenv('environment');
        if ($env === false) {
            $errorMessage = 'Can\'t determine configuration environment.';
            Logger::getLogger(__CLASS__)->error($errorMessage);
            throw new RuntimeException($errorMessage);
        }
    }
}
