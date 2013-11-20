<?php
namespace Ouzo\Db\Dialect;

use Ouzo\Config;

class DialectFactory
{

    public static function create()
    {
        $dialectClass = Config::getValue('sql_dialect');
        return new $dialectClass();
    }
}