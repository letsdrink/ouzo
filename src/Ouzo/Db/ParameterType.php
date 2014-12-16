<?php
namespace Ouzo\Db;

use PDO;

class ParameterType
{
    public static function getType($param)
    {
        if (is_int($param)) {
            return PDO::PARAM_INT;
        }
        if (is_bool($param)) {
            return PDO::PARAM_BOOL;
        }
        return PDO::PARAM_STR;
    }
}
