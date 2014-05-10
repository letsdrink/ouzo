<?php
namespace Ouzo\Db\Dialect;

use Ouzo\Db\QueryType;
use Ouzo\Utilities\Arrays;

class MySqlDialect extends Dialect
{
    public function from()
    {
        return $this->_buildFrom($this->_query->type, $this->_query->table);
    }

    private function _buildFrom($type, $table)
    {
        $alias = $this->_query->aliasTable;
        if ($alias) {
            $aliasOperator = $type == QueryType::$DELETE ? '' : ' AS ';
            return " FROM $table" . $aliasOperator . $alias;
        }
        return " FROM $table";
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