<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Ouzo\Config;
use Ouzo\Utilities\Objects;
use Symfony\Component\Console\Input\InputInterface;

class MigrationDbConfig
{
    private $dbConfig = [];

    public function __construct(InputInterface $input)
    {
        $this->dbConfig['dbname'] = $input->getOption('db_name') ?: Config::getValue('db', 'dbname');
        $this->dbConfig['user'] = $input->getOption('db_user') ?: Config::getValue('db', 'user');
        $this->dbConfig['pass'] = $input->getOption('db_pass') ?: Config::getValue('db', 'pass');
        $this->dbConfig['host'] = $input->getOption('db_host') ?: Config::getValue('db', 'host');
        $this->dbConfig['port'] = $input->getOption('db_port') ?: Config::getValue('db', 'port');
        $this->dbConfig['driver'] = $input->getOption('db_driver') ?: Config::getValue('db', 'driver');
    }

    public function getUser(): string
    {
        return $this->dbConfig['user'];
    }

    public function getHost(): string
    {
        return $this->dbConfig['host'];
    }

    public function getPort(): string
    {
        return $this->dbConfig['port'];
    }

    public function getDbName(): string
    {
        return $this->dbConfig['dbname'];
    }

    public function __toString(): string
    {
        return Objects::toString($this->dbConfig);
    }

    public function toArray(): array
    {
        return $this->dbConfig;
    }
}