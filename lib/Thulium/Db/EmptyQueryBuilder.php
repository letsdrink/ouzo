<?php

namespace Thulium\Db;


class EmptyQueryBuilder implements QueryBuilder {


    public function from($table = null)
    {
        return $this;
    }

    public function where($where = '', $values)
    {
        return $this;
    }

    public function order($value)
    {
        return $this;
    }

    public function offset($value)
    {
        return $this;
    }

    public function limit($value)
    {
        return $this;
    }

    public function join($joinTable, $joinKey, $idName)
    {
        return $this;
    }

    public function fetch()
    {
        return array();
    }

    public function fetchAll()
    {
        return array();
    }

    public function delete()
    {
        return 0;
    }

    public function fetchFirst()
    {
        return 0;
    }
}