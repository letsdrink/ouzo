<?php

namespace Thulium\Db;


use InvalidArgumentException;
use PDO;
use Thulium\DbException;
use Thulium\Logger;
use Thulium\Utilities\Arrays;
use Thulium\Utilities\Objects;

class QueryExecutor
{

    private $_db;
    private $_adapter;
    private $_query;
    private $_boundValues = array();

    public $_sql;
    public $_preparedQuery;
    public $_fetchStyle = PDO::FETCH_ASSOC;

    function __construct($db, $query)
    {
        $this->_db = $db;
        $this->_query = $query;

        $this->_adapter = new PostgresDialect();
    }

    public static function prepare($db, $query)
    {
        if (empty($query->table)) {
            throw new InvalidArgumentException($query->table . " cannot be empty");
        }
        if (QueryExecutor::isEmptyResult($query->where)) {
            return new EmptyQueryExecutor();
        }

        return new QueryExecutor($db, $query);
    }

    public function fetch()
    {
        $this->_buildQuery();
        return $this->_fetch('fetch');
    }

    public function fetchAll()
    {
        $this->_buildQuery();
        return $this->_fetch('fetchAll');
    }

    public function delete()
    {
        $this->_query->type = QueryType::$DELETE;
        $this->_buildQuery();
        $this->_db->query($this->_sql, $this->_boundValues);
        return $this->_db->query->rowCount();
    }

    public function count()
    {
        $this->_query->type = QueryType::$COUNT;
        $this->_query->selectColumns = 'count(*)';
        return Arrays::firstOrNull($this->fetch());
    }

    private function _fetch($function)
    {
        $obj = $this;
        return Stats::trace($this->_sql, $this->_boundValues, function () use ($obj, $function) {
            $obj->_prepareAndBind();

            Logger::getSqlLogger()
                ->addInfo($obj->getSql(), $obj->getBoundValues());

            if (!$obj->_preparedQuery->execute()) {
                throw new DbException('Exception: query: ' . $obj->getSql() . ' with params: (' . implode(', ', $obj->getBoundValues()) . ') failed: ' . $obj->lastErrorMessage());
            }
            return $obj->_preparedQuery->$function($obj->_fetchStyle);
        });
    }

    public function _prepareAndBind()
    {
        $this->_preparedQuery = $this->_db->_dbHandle->prepare($this->_sql);
        foreach ($this->_boundValues as $key => $valueBind) {
            $this->_preparedQuery->bindValue($key + 1, $valueBind);
        }
    }

    public function getSql()
    {
        return $this->_sql;
    }

    public function getBoundValues()
    {
        return $this->_boundValues;
    }

    public function lastErrorMessage()
    {
        return $this->_db->lastErrorMessage();
    }

    private function _buildQuery()
    {
        if (!empty($this->_query->selectColumns)) {
            $this->_fetchStyle = PDO::FETCH_NUM;
        }
        $this->_addBindValues();
        $this->_sql = $this->_adapter->buildQuery($this->_query);
    }

    public function _addBindValue($value)
    {
        if (is_array($value)) {
            $this->_boundValues = array_merge($this->_boundValues, $value);
        } else {
            $this->_boundValues[] = is_bool($value) ? Objects::booleanToString($value) : $value;
        }
    }

    private function _buildWhereValues($where, $values)
    {
        return is_array($where) ? Arrays::flatten(array_values($where)) : $values;
    }

    private static function isEmptyResult($where)
    {
        if (is_array($where)) {
            foreach ($where as $value) {
                if (is_array($value) && sizeof($value) == 0) {
                    return true;
                }
            }
        }
        return false;
    }

    private function _addBindValues()
    {
        $values = $this->_buildWhereValues($this->_query->where, $this->_query->whereValues);
        if (!empty($this->_query->where)) {
            $this->_addBindValue($values);
        }
        if ($this->_query->limit) {
            $this->_addBindValue($this->_query->limit);
        }
        if ($this->_query->offset) {
            $this->_addBindValue($this->_query->offset);
        }
    }
}