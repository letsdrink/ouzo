<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Db\WhereClause\WhereClause;
use PDO;

class Query
{
    public $table;
    public $aliasTable;
    public $distinct = false;
    public $selectColumns;
    public $selectType = PDO::FETCH_ASSOC;
    public $order;
    public $limit;
    public $offset;
    public $updateAttributes = array();
    public $whereClauses = array();
    public $joinClauses = array();
    public $usingClauses = array();
    public $type;
    public $options = array();
    public $groupBy;
    public $lockForUpdate = false;
    public $comment;

    public function __construct($type = null)
    {
        $this->type = $type ? $type : QueryType::$SELECT;
    }

    public static function newInstance($type = null)
    {
        return new Query($type);
    }

    public static function insert($attributes)
    {
        return Query::newInstance(QueryType::$INSERT)->attributes($attributes);
    }

    public static function update($attributes)
    {
        return Query::newInstance(QueryType::$UPDATE)->attributes($attributes);
    }

    public static function select($selectColumns = null)
    {
        $query = new Query();
        $query->selectColumns = $selectColumns;
        return $query;
    }

    public static function selectDistinct($selectColumns = null)
    {
        $query = self::select($selectColumns);
        $query->distinct = true;
        return $query;
    }

    public static function count()
    {
        return new Query(QueryType::$COUNT);
    }

    public static function delete()
    {
        return new Query(QueryType::$DELETE);
    }

    public function attributes($attributes)
    {
        $this->updateAttributes = $attributes;
        return $this;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function into($table)
    {
        return $this->table($table);
    }

    public function from($table, $alias = null)
    {
        $this->aliasTable = $alias;
        return $this->table($table);
    }

    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function where($where = '', $whereValues = null)
    {
        $this->whereClauses[] = WhereClause::create($where, $whereValues);
        return $this;
    }

    public function addUsing(JoinClause $usingClause)
    {
        $this->usingClauses[] = $usingClause;
        return $this;
    }

    public function join($joinTable, $joinKey, $idName, $alias = null, $type = 'LEFT', $on = array())
    {
        $onClauses = array(WhereClause::create($on));
        $this->joinClauses[] = new JoinClause($joinTable, $joinKey, $idName, $this->aliasTable ?: $this->table, $alias, $type, $onClauses);
        return $this;
    }

    public function addJoin(JoinClause $join)
    {
        $this->joinClauses[] = $join;
        return $this;
    }

    public function groupBy($groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    public function lockForUpdate()
    {
        $this->lockForUpdate = true;
        return $this;
    }

    public function comment($comment)
    {
        $this->comment = $comment;
        return $this;
    }
}
