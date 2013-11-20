<?php
namespace Ouzo\Db\Dialect;

use Ouzo\Utilities\Arrays;

class PostgresDialect extends Dialect
{
    public function getExceptionForError($errorInfo)
    {
        $connectionErrorCodes = array('57000', '57014', '57P01', '57P02', '57P03');
        $errorCode = Arrays::getValue($errorInfo, 0);
        if (in_array($errorCode, $connectionErrorCodes))
            return '\Ouzo\DbConnectionException';
        return parent::getExceptionForError($errorInfo);
    }
}