<?php
namespace Ouzo\Db\Dialect;

use Ouzo\Utilities\FluentArray;

class PostgresDialect extends Dialect
{
    public function buildQuery($query)
    {
        $this->_query = $query;

        $sql = DialectUtil::buildQueryPrefix($query->type);
        $sql .= $this->select();
        $sql .= $this->from();
        $sql .= $this->join();
        $sql .= $this->where();
        $sql .= $this->order();
        $sql .= $this->limit();
        $sql .= $this->offset();

        return rtrim($sql);
    }

    public function from()
    {
        $alias = DialectUtil::buildFromWithAlias($this->_query->aliasTable);
        return ' FROM ' . $this->_query->table . ' AS ' . $alias;
    }
}