<?php
namespace Ouzo\Db\Dialect;

use Ouzo\Config;

class DialectFactory
{
    /**
     * @return Dialect
     */
    public static function create()
    {
        $dialectClass = Config::getValue('sql_dialect');
        return new $dialectClass();
    }
}