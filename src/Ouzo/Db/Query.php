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
    public $updateAttributes = array();
    public $whereClauses = array();
    public $joinClauses = array();
    public $type;

    function __construct($type = null)
    {
        $this->type = $type ? $type : QueryType::$SELECT;
    }

    static function newInstance($type = null)
    {
        return new Query($type);
    }

    static function insert($attributes)
    {
        return Query::newInstance(QueryType::$INSERT)->attributes($attributes);
    }

    static function update($attributes)
    {
        return Query::newInstance(QueryType::$UPDATE)->attributes($attributes);
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

    function attributes($attributes)
    {
        $this->updateAttributes = $attributes;
        return $this;
    }

    function table($table)
    {
        $this->table = $table;
        return $this;
    }

    function into($table)
    {
        return $this->table($table);
    }

    function from($table)
    {
        return $this->table($table);
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

    function join($joinTable, $joinKey, $idName, $alias = null, $type = 'LEFT', $on = array())
    {
        $this->joinClauses[] = new JoinClause($joinTable, $joinKey, $idName, $this->aliasTable ? : $this->table, $alias, $type, new WhereClause($on, array()));
        return $this;
    }

    function addJoin(JoinClause $join)
    {
        $this->joinClauses[] = $join;
        return $this;
    }
}