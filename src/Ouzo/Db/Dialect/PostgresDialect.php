<?php
namespace Ouzo\Db\Dialect;

use Ouzo\Utilities\Arrays;

class PostgresDialect extends Dialect
{
    public function getConnectionErrorCodes()
    {
        return array('57000', '57014', '57P01', '57P02', '57P03');
    }

    public function getErrorCode($errorInfo)
    {
        return Arrays::getValue($errorInfo, 0);
    }
}
