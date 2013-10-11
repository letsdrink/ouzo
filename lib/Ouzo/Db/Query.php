<?php
namespace Ouzo\Db;

use PDO;

class Query
{
    public $table;
    public $aliasTable;
    public $selectColumns;
    public $selectType = PDO::FETCH_ASSOC;
    public $order;
    public $limit;
    public $offset;
    public $whereClauses = array();
    public $joinClauses = array();
    public $type;

    function __construct($type = null)
    {
        $this->type = $type ? $type : QueryType::$SELECT;
    }

    static function select($selectColumns = null)
    {
        $query = new Query();
        $query->selectColumns = $selectColumns;
        return $query;
    }

    static function count()
    {
        return new Query(QueryType::$COUNT);
    }

    static function delete()
    {
        return new Query(QueryType::$DELETE);
    }

    function from($table)
    {
        $this->table = $table;
        return $this;
    }

    function order($order)
    {
        $this->order = $order;
        return $this;
    }

    function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    function where($where = '', $whereValues = null)
    {
        $this->whereClauses[] = new WhereClause($where, $whereValues);
        return $this;
    }

    function join($joinTable, $joinKey, $idName)
    {
        $this->joinClauses[] = new JoinClause($joinTable, $joinKey, $idName, $this->table);
        return $this;
    }
}