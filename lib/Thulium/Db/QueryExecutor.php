<?php

namespace Thulium\Db;


use InvalidArgumentException;
use PDO;
use Thulium\DbException;
use Thulium\Logger;
use Thulium\Utilities\Arrays;
use Thulium\Utilities\Objects;

class QueryExecutor {

    private $_db;
    private $_adapter;
    private $query;

    private $_queryValues = array();
    public $_fetchStyle = PDO::FETCH_ASSOC;
    public $_queryPrepared;
    public $_sql;

    function __construct($db, $query)
    {
        $this->_db = $db;
        $this->query = $query;

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

    public function fetchFirst()
    {
        $result = $this->fetch();
        return Arrays::firstOrNull($result);
    }

    public function fetch()
    {
        $this->_buildQuery(true);
        return $this->_fetch('fetch');
    }

    public function fetchAll()
    {
        $this->_buildQuery(true);
        return $this->_fetch('fetchAll');
    }

    public function delete()
    {
        $this->_buildQuery(false);
        $this->_db->query($this->_sql, $this->_queryValues);
        return $this->_db->query->rowCount();
    }

    public function count()
    {
        $this->query->selectColumns = 'count(*)';
        return $this->fetchFirst();
    }

    private function _fetch($function)
    {
        $obj = $this;
        return Stats::trace($this->_sql, $this->_queryValues, function () use ($obj, $function) {
            $obj->_prepareAndBind();

            Logger::getSqlLogger()
                ->addInfo($obj->getSql(), $obj->getQueryValues());

            if (!$obj->_queryPrepared->execute()) {
                throw new DbException('Exception: query: ' . $obj->getSql() . ' with params: (' . implode(', ', $obj->getQueryValues()) . ') failed: ' . $obj->lastErrorMessage());
            }
            return $obj->_queryPrepared->$function($obj->_fetchStyle);
        });
    }

    public function _prepareAndBind()
    {
        $this->_queryPrepared = $this->_db->_dbHandle->prepare($this->_sql);
        foreach ($this->_queryValues as $key => $valueBind) {
            $this->_queryPrepared->bindValue($key + 1, $valueBind);
        }
    }

    public function getSql()
    {
        return $this->_sql;
    }

    public function getQueryValues()
    {
        return $this->_queryValues;
    }

    public function lastErrorMessage()
    {
        return $this->_db->lastErrorMessage();
    }

    private function _buildQuery($select)
    {
        if (!empty($this->query->selectColumns)) {
            $this->_fetchStyle = PDO::FETCH_NUM;
        }

        $values = $this->_buildWhereValues($this->query->where, $this->query->whereValues);
        if (!empty($this->query->where)) {
            $this->_addBindValue($values);
        }

        if ($this->query->limit) {
            $this->_addBindValue($this->query->limit);
        }
        if ($this->query->offset) {
            $this->_addBindValue($this->query->offset);
        }

        $this->_sql = $this->_adapter->buildQuery($select, $this->query);
    }

    public function _addBindValue($value)
    {
        if (is_array($value)) {
            $this->_queryValues = array_merge($this->_queryValues, $value);
        } else {
            $this->_queryValues[] = is_bool($value) ? Objects::booleanToString($value) : $value;
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
}