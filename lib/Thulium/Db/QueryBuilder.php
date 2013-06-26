<?php

namespace Thulium\Db;


use Thulium\Utilities\Objects;

interface QueryBuilder
{

    public function from($table = null);

    public function where($where = '', $values);

    public function order($value);

    public function offset($value);

    public function limit($value);

    public function join($joinTable, $joinKey, $idName);

    public function fetchFirst();

    public function fetch();

    public function fetchAll();

    public function delete();
}