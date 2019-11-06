<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Command;

use Exception;
use Ouzo\Config;
use Ouzo\Utilities\Objects;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationSqlImporterCommand extends Command
{
    /* @var InputInterface */
    private $input;
    /* @var OutputInterface */
    private $output;
    /* @var array */
    private $dbConfig = [];

    public function configure()
    {
        $this->setName('migration:sql_import')
            ->addOption('db_name', 'N', InputOption::VALUE_REQUIRED, 'Database name')
            ->addOption('db_user', 'U', InputOption::VALUE_REQUIRED, 'Database user')
            ->addOption('db_pass', 'S', InputOption::VALUE_REQUIRED, 'Database password')
            ->addOption('db_host', 'H', InputOption::VALUE_REQUIRED, 'Database host')
            ->addOption('db_port', 'P', InputOption::VALUE_REQUIRED, 'Database port')
            ->addArgument('files', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'SQL file to import');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->dbConfig['dbname'] = $this->input->getOption('db_name') ?: Config::getValue('db', 'dbname');
        $this->dbConfig['user'] = $this->input->getOption('db_user') ?: Config::getValue('db', 'user');
        $this->dbConfig['pass'] = $this->input->getOption('db_pass') ?: Config::getValue('db', 'pass');
        $this->dbConfig['host'] = $this->input->getOption('db_host') ?: Config::getValue('db', 'host');
        $this->dbConfig['port'] = $this->input->getOption('db_port') ?: Config::getValue('db', 'port');

        $files = $this->input->getArgument('files');

        $dbConfig = Objects::toString($this->dbConfig);
        $this->output->writeln("Databae: {$dbConfig}");
        foreach ($files as $file) {
            $this->import($file);
        }
    }

    public function import($file)
    {
        $this->output->write("<info>Importing file {$file}... </info>");
        $command = "psql -e -U {$this->dbConfig['user']} -h {$this->dbConfig['host']} -p {$this->dbConfig['port']} -f {$file} {$this->dbConfig['dbname']} 2>&1";
        exec($command, $output, $returnStatus);

        if ($returnStatus) {
            $this->output->writeln('<error>ERROR</error>');
            $reason = Objects::toString(array_slice($output, -3));
            throw new Exception("Error executing command {$command}\n\nReason: {$reason}\n\n");
        }

        $this->output->writeln('<comment>DONE</comment>');
    }
}