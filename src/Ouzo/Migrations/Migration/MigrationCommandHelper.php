<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

class MigrationCommandHelper
{
    public static function addDbOptions(Command $command): Command
    {
        return $command->addOption('db_name', 'N', InputOption::VALUE_REQUIRED, 'Database name')
            ->addOption('db_user', 'U', InputOption::VALUE_REQUIRED, 'Database user')
            ->addOption('db_pass', 'S', InputOption::VALUE_REQUIRED, 'Database password')
            ->addOption('db_host', 'H', InputOption::VALUE_REQUIRED, 'Database host')
            ->addOption('db_port', 'P', InputOption::VALUE_REQUIRED, 'Database port')
            ->addOption('db_driver', 'D', InputOption::VALUE_REQUIRED, 'Database driver');
    }
}