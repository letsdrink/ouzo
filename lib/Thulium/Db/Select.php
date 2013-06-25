<?php
namespace Thulium\Db;

use InvalidArgumentException;
use PDO;
use Thulium\Db;
use Thulium\DbException;
use Thulium\Logger;
use Thulium\Utilities\FluentArray;
use Thulium\Utilities\Joiner;
use Thulium\Utilities\Objects;

class Select
{
    private $_offset = null;
    private $_limit = null;
    private $_where = '';
    private $_db = null;
    private $_query = 'SELECT ';
    private $_queryValues = array();
    public $_fetchStyle = PDO::FETCH_ASSOC;

    public $queryPrepared = null;

    public function __construct(Db $dbHandle, array $columns = array())
    {
        if ($dbHandle instanceof Db) {
            $this->_db = $dbHandle;
        } else {
            throw new DbSelectException('Wrong database handler');
        }

        $this->columns($columns);
    }

    public function _prepareAndBind()
    {
        $this->queryPrepared = $this->_db->_dbHandle->prepare($this->_query);
        foreach ($this->_queryValues as $key => $valueBind) {
            $this->queryPrepared->bindValue($key + 1, $valueBind);
        }
    }

    private function _addBindValue($value)
    {
        if (is_array($value)) {
            $this->_queryValues = array_merge($this->_queryValues, $value);
        } else {
            $this->_queryValues[] = is_bool($value) ? Objects::booleanToString($value) : $value;
        }
    }

    private function columns(array $columns = array())
    {
        if (!empty($columns)) {
            $this->_fetchStyle = PDO::FETCH_NUM;

            $buildColumns = Joiner::on(', ')->map($this->addAliases())->join($columns);
        } else {
            $buildColumns = 'main.*';
        }

        $this->_query .= $buildColumns;
        return $this;
    }

    public function from($table = null)
    {
        if (empty($table)) {
            throw new InvalidArgumentException("$table cannot be empty");
        }

        $buildTable = '';

        if (is_array($table)) {
            foreach ($table as $tableName => $alias) {
                $buildTable .= $tableName . ' AS ' . $alias;
            }
        } else if (is_string($table)) {
            $buildTable .= $table . ' AS main ';
        }

        $this->_query .= ' FROM ' . $buildTable;
        return $this;
    }

    public function where($columnValue = '', $value)
    {
        if (!empty($columnValue)) {
            $this->_query .= empty($this->_where) ? ' WHERE ' : ' AND ';
            $this->_query .= stripos($columnValue, 'OR') ? '(' . $columnValue . ')' : $columnValue;
            $this->_addBindValue($value);
            $this->_where .= $columnValue;
        }

        return $this;
    }

    public function order($value)
    {
        if ($value) {
            $this->_query .= ' ORDER BY ' . (is_array($value) ? implode(', ', $value) : $value);
        }
        return $this;
    }

    public function offset($value)
    {
        if ($value) {
            $offset = intval($value);
            if ($this->_offset === null) {
                $this->_offset = $offset;
                $this->_query .= " OFFSET ? ";
                $this->_addBindValue($value);
            }
        }
        return $this;
    }

    public function limit($value)
    {
        if ($value) {
            $limit = intval($value);
            if ($this->_limit === null) {
                $this->_limit = $limit;
                $this->_query .= " LIMIT ? ";
                $this->_addBindValue($value);
            }
        }
        return $this;
    }

    public function join($joinTable, $joinKey, $idName)
    {
        if (!empty($joinTable)) {
            $this->_query .= ' LEFT JOIN ' . $joinTable . ' AS joined ON joined.' . $joinKey . ' = main.' . $idName;
        }
        return $this;
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

            if (!$obj->queryPrepared->execute()) {
                throw new DbException('Exception: query: ' . $obj->getQuery() . ' with params: (' . implode(', ', $obj->getQueryValues()) . ') failed: ' . $obj->lastErrorMessage());
            }
            return $obj->queryPrepared->$function($obj->_fetchStyle);
        });
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

    public function fetch()
    {
        return $this->_fetch('fetch');
    }

    public function fetchAll()
    {
        return $this->_fetch('fetchAll');
    }

    private function addAliases()
    {
        return function ($alias, $column) {
            return $column . (is_string($alias) ? ' AS ' . $alias : '');
        };
    }
}

class DbSelectException extends \Exception
{
}