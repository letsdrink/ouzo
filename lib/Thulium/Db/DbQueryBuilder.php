<?php

namespace Thulium\Db;


use Exception;
use InvalidArgumentException;
use PDO;
use Thulium\Db;
use Thulium\DbException;
use Thulium\Logger;
use Thulium\Utilities\Arrays;
use Thulium\Utilities\Objects;

class DbQueryBuilder implements QueryBuilder
{

    private $_adapter;
    private $_db = null;
    private $_query;
    private $_queryValues = array();
    public $_fetchStyle = PDO::FETCH_ASSOC;
    public $_queryPrepared;

    private $_sql_table;
    private $_sql_columns;
    private $_sql_delete;
    private $_sql_joinTable;
    private $_sql_joinKey;
    private $_sql_idName;
    private $_sql_order;
    private $_sql_limit;
    private $_sql_offset;
    private $_sql_where;

    public function __construct(Db $dbHandle)
    {
        if (!$dbHandle instanceof Db) {
            throw new Exception('Wrong database handler');
        }

        $this->_db = $dbHandle;
        $this->_adapter = new PostgresAdapter();
    }

    public function __toString()
    {
        return $this->_query;
    }

    private function _fetch($function)
    {
        $obj = $this;
        return Stats::trace($this->_query, $this->_queryValues, function () use ($obj, $function) {
            $obj->_prepareAndBind();

            Logger::getSqlLogger()
                ->addInfo($obj->getQuery(), $obj->getQueryValues());

            if (!$obj->_queryPrepared->execute()) {
                throw new DbException('Exception: query: ' . $obj->getQuery() . ' with params: (' . implode(', ', $obj->getQueryValues()) . ') failed: ' . $obj->lastErrorMessage());
            }
            return $obj->_queryPrepared->$function($obj->_fetchStyle);
        });
    }

    public function _prepareAndBind()
    {
        $this->_queryPrepared = $this->_db->_dbHandle->prepare($this->_query);
        foreach ($this->_queryValues as $key => $valueBind) {
            $this->_queryPrepared->bindValue($key + 1, $valueBind);
        }
    }

    public function getQuery()
    {
        return $this->_query;
    }

    public function getQueryValues()
    {
        return $this->_queryValues;
    }

    public function lastErrorMessage()
    {
        return $this->_db->lastErrorMessage();
    }

    public function fetchFirst()
    {
        $result = $this->fetch();
        return $result[0];
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
        $this->_buildQuery();
        $this->_db->query($this->_query, $this->_queryValues);
        return $this->_db->query->rowCount();
    }

    private function _buildQuery()
    {
        $this->_query = $this->_adapter->buildQuery($this->_sql_delete,
            $this->_sql_columns, $this->_sql_table, $this->_sql_joinTable,
            $this->_sql_joinKey, $this->_sql_idName, $this->_sql_order,
            $this->_sql_limit, $this->_sql_offset, $this->_sql_where);
    }

    public function _addBindValue($value)
    {
        if (is_array($value)) {
            $this->_queryValues = array_merge($this->_queryValues, $value);
        } else {
            $this->_queryValues[] = is_bool($value) ? Objects::booleanToString($value) : $value;
        }
    }

    private function _columns(array $columns = array())
    {
        $this->_sql_columns = $columns;
        if (!empty($columns)) {
            $this->_fetchStyle = PDO::FETCH_NUM;
        }
        return $this;
    }

    public function from($table = null)
    {
        if (empty($table)) {
            throw new InvalidArgumentException("$table cannot be empty");
        }
        $this->_sql_table = $table;
        return $this;
    }

    public function where($where = '', $values)
    {
        if ($this->isEmptyResult($where)) {
            return new EmptyQueryBuilder();
        }

        $this->_sql_where = $where;

        $values = $this->_buildWhereValues($where, $values);
        if (!empty($where)) {
            $this->_addBindValue($values);
        }
        return $this;
    }

    public function order($value)
    {
        $this->_sql_order = $value;
        return $this;
    }

    public function offset($value)
    {
        $this->_sql_offset = $value;
        if ($value) {
            $this->_addBindValue($value);
        }
        return $this;
    }

    public function limit($value)
    {
        $this->_sql_limit = $value;
        if ($value) {
            $this->_addBindValue($value);
        }
        return $this;
    }

    public function join($joinTable, $joinKey, $idName)
    {
        $this->_sql_joinTable = $joinTable;
        $this->_sql_joinKey = $joinKey;
        $this->_sql_idName = $idName;
        return $this;
    }

    private function _buildWhereValues($where, $values)
    {
        return is_array($where) ? Arrays::flatten(array_values($where)) : $values;
    }

    private function isEmptyResult($where)
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

    public function select($columns)
    {
        $this->_columns($columns);
        return $this;
    }

    public function deleteQuery()
    {
        $this->_sql_delete = true;
        return $this;
    }
}