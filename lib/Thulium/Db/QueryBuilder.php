<?php
namespace Thulium\Db;

use InvalidArgumentException;
use PDO;
use Thulium\Db;
use Thulium\DbException;
use Thulium\Logger;
use Thulium\Utilities\Arrays;
use Thulium\Utilities\FluentArray;
use Thulium\Utilities\Joiner;
use Thulium\Utilities\Objects;

class QueryBuilder
{
    private $_db = null;
    private $_query;
    private $_queryValues = array();
    public $_fetchStyle = PDO::FETCH_ASSOC;
    private $_delete;

    public $queryPrepared = null;

    public function __construct(Db $dbHandle, array $columns = array(), $delete = false)
    {
        if ($dbHandle instanceof Db) {
            $this->_db = $dbHandle;
        } else {
            throw new DbSelectException('Wrong database handler');
        }

        $this->_delete = $delete;
        $this->_query = $delete ? 'DELETE ' : 'SELECT ';

        $this->columns($columns);
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
        if (!$this->_delete) {
            if (!empty($columns)) {
                $this->_fetchStyle = PDO::FETCH_NUM;
                $this->_query .= Joiner::on(', ')->map($this->addAliases())->join($columns);
            } else {
                $this->_query .= 'main.*';
            }
        }
        return $this;
    }

    public function from($table = null)
    {
        if (empty($table)) {
            throw new InvalidArgumentException("$table cannot be empty");
        }
        $this->_query .= ' FROM ' . $table . ' AS main ';
        return $this;
    }

    public function where($where = '', $values)
    {
        list ($where, $values) = $this->_buildWhereQuery($where, $values);

        if (!empty($where)) {
            $this->_query .= ' WHERE ' . (stripos($where, 'OR') ? '(' . $where . ')' : $where);
            $this->_addBindValue($values);
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
            $this->_query .= " OFFSET ? ";
            $this->_addBindValue($value);
        }
        return $this;
    }

    public function limit($value)
    {
        if ($value) {
            $this->_query .= " LIMIT ? ";
            $this->_addBindValue($value);
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

    public function _prepareAndBind()
    {
        $this->queryPrepared = $this->_db->_dbHandle->prepare($this->_query);
        foreach ($this->_queryValues as $key => $valueBind) {
            $this->queryPrepared->bindValue($key + 1, $valueBind);
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

    public function fetch()
    {
        return $this->_fetch('fetch');
    }

    public function fetchAll()
    {
        return $this->_fetch('fetchAll');
    }

    public function delete()
    {
        $this->_db->query($this->_query, $this->_queryValues);
    }

    private function addAliases()
    {
        return function ($alias, $column) {
            return $column . (is_string($alias) ? ' AS ' . $alias : '');
        };
    }

    private function _buildWhereQuery($where, $values)
    {
        return is_array($where) ? $this->_whereArray($where) : array($where, $values);
    }

    private function _whereArray($params)
    {
        $where = implode(' AND ', $this->_buildWhereKeys($params));
        $values = Arrays::flatten(array_values($params));
        return array($where, $values);
    }

    private function _buildWhereKeys($params)
    {
        $keys = array();
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $in = implode(', ', array_fill(0, count($value), '?'));
                $keys[] = $key . ' IN (' . $in . ')';
            } else {
                $keys[] = $key . ' = ?';
            }
        }
        return $keys;
    }
}

class DbSelectException extends \Exception
{
}