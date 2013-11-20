<?php
namespace Ouzo\Db\Dialect;

class PostgresDialect extends Dialect
{
    public function getExceptionForErrorCode($errorCode)
    {
        $connectionErrorCodes = array(7);
        if (in_array($errorCode, $connectionErrorCodes))
            return '\Ouzo\DbConnectionException';
        return parent::getExceptionForErrorCode($errorCode);
    }
}