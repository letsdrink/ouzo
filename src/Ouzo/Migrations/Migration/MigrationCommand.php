<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;


use Ouzo\Config;
use Ouzo\Logger\DefaultMessageFormatter;
use Ouzo\Logger\StdOutputLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


abstract class MigrationCommand extends Command
{

    const LOG_STDOUT_ARGUMENT = 'log-stdout';

    protected function configure()
    {
        $this->addOption('db_name', 'N', InputOption::VALUE_REQUIRED, 'Database name')
            ->addOption('db_user', 'U', InputOption::VALUE_REQUIRED, 'Database user')
            ->addOption('db_pass', 'S', InputOption::VALUE_REQUIRED, 'Database password')
            ->addOption('db_host', 'H', InputOption::VALUE_REQUIRED, 'Database host')
            ->addOption('db_port', 'P', InputOption::VALUE_REQUIRED, 'Database port')
            ->addOption('db_driver', 'D', InputOption::VALUE_REQUIRED, 'Database driver');

        $this->addOption(self::LOG_STDOUT_ARGUMENT, 'L', InputOption::VALUE_NONE, 'Print sql queries to stdout');
        $this->configureCommand();
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        if ($input->getOption(self::LOG_STDOUT_ARGUMENT)) {
            Config::overrideProperty('logger', 'default', 'class')->with(StdOutputLogger::class);
            Config::overrideProperty('logger', 'default', 'formatter')->with(DefaultMessageFormatter::class);
        }
        return $this->executeCommand($input, $output);
    }

    protected abstract function executeCommand(InputInterface $input, OutputInterface $output);

    protected abstract function configureCommand();
}