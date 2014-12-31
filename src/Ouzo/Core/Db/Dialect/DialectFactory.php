<?php
namespace Ouzo\Db\Dialect;

use Exception;
use Ouzo\Config;

class DialectFactory
{
    /**
     * @return Dialect
     * @throws Exception
     */
    public static function create()
    {
        $dialectClass = Config::getValue('sql_dialect');
        if (!$dialectClass) {
            throw new Exception('SQL dialect is not given. Please check config option - sql_dialect.');
        }
        $dialectClass = new $dialectClass();
        if (!$dialectClass instanceof Dialect) {
            throw new Exception('Invalid sql dialect. Dialect should extends from Dialect class.');
        }
        return $dialectClass;
    }
}
