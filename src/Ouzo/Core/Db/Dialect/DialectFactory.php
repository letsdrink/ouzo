<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\Dialect;

use Exception;
use Ouzo\Config;

class DialectFactory
{
    public static function create(): Dialect
    {
        $dialectClass = Config::getValue('sql_dialect');
        if (!$dialectClass) {
            throw new Exception('SQL dialect was not found in config. Please, check for option - sql_dialect.');
        }
        $dialectClass = new $dialectClass();
        if (!$dialectClass instanceof Dialect) {
            throw new Exception('Invalid sql_dialect. Dialect have to extend Dialect class.');
        }
        return $dialectClass;
    }
}
