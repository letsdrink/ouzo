<?php
namespace Ouzo\Db\Dialect;

use Ouzo\Db\QueryType;
use Ouzo\Utilities\Arrays;

class MySqlDialect extends Dialect
{
    public function table()
    {
        $alias = $this->_query->aliasTable;
        if ($alias) {
            $aliasOperator = $this->_query->type == QueryType::$DELETE ? '' : ' AS ';
            return $this->_query->table . $aliasOperator . $alias;
        }
        return $this->_query->table;
    }

    public function getConnectionErrorCodes()
    {
        return array(2003, 2006);
    }

    public function getErrorCode($errorInfo)
    {
        return Arrays::getValue($errorInfo, 1);
    }
}
