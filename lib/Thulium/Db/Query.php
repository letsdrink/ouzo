<?php

namespace Thulium\Db;


class Query {

    public $table;
    public $selectColumns;
    public $joinTable;
    public $joinKey;
    public $idName;
    public $order;
    public $limit;
    public $offset;
    public $where;
    public $whereValues;
}