<?php

namespace Thulium\Db;


use Exception;
use PDO;
use Thulium\Db;
use Thulium\DbException;
use Thulium\Logger;
use Thulium\Utilities\Objects;

abstract class DbQueryBuilder implements QueryBuilder
{

    protected $_db = null;
    protected $_query;
    protected $_queryValues = array();
    public $_fetchStyle = PDO::FETCH_ASSOC;
    public $_queryPrepared;

    public function __construct(Db $dbHandle)
    {
        if (!$dbHandle instanceof Db) {
            throw new Exception('Wrong database handler');
        }

        $this->_db = $dbHandle;
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
        return $this->_fetch('fetch');
    }

    public function fetchAll()
    {
        return $this->_fetch('fetchAll');
    }

    public function delete()
    {
        $this->_db->query($this->_query, $this->_queryValues);
        return $this->_db->query->rowCount();
    }

    public function _addBindValue($value)
    {
        if (is_array($value)) {
            $this->_queryValues = array_merge($this->_queryValues, $value);
        } else {
            $this->_queryValues[] = is_bool($value) ? Objects::booleanToString($value) : $value;
        }
    }
}