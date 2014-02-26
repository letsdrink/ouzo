<?php
namespace Ouzo\Db\Dialect;

use BadMethodCallException;
use Ouzo\Db\JoinClause;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Sqlite3Dialect extends Dialect
{
    function getConnectionErrorCodes()
    {
        return array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 100, 101);
    }

    function getErrorCode($errorInfo)
    {
        return Arrays::getValue($errorInfo, 1);
    }

    public function join()
    {
        $any = Arrays::any($this->_query->joinClauses, function (JoinClause $joinClause) {
            return Strings::equalsIgnoreCase($joinClause->type, 'RIGHT');
        });
        if ($any) {
            throw new BadMethodCallException('RIGHT JOIN is not supported in sqlite3.');
        }
        return parent::join();
    }
}