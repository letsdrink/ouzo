<?php
namespace Ouzo\Db;

use Ouzo\Logger\Logger;
use Ouzo\Utilities\Objects;
use PDO;

class StatementExecutor
{
    private $_sql;
    private $_humanizedSql;
    private $_dbHandle;
    private $_boundValues;
    /**
     * @var PDOExecutor
     */
    private $_pdoExecutor;

    private function __construct($dbHandle, $sql, $boundValues, $pdoExecutor)
    {
        $this->_boundValues = $boundValues;
        $this->_dbHandle = $dbHandle;
        $this->_sql = $sql;
        $this->_humanizedSql = QueryHumanizer::humanize($sql);
        $this->_pdoExecutor = $pdoExecutor;
    }

    private function _execute($afterCallback)
    {
        $obj = $this;
        return Stats::trace($this->_humanizedSql, $this->_boundValues, function () use ($obj, $afterCallback) {
            return $obj->_internalExecute($afterCallback);
        });
    }

    public function _internalExecute($afterCallback)
    {
        $sqlString = $this->_humanizedSql . ' with params: '. Objects::toString($this->_boundValues);
        Logger::getLogger(__CLASS__)->info("Query: %s", array($sqlString));

        $pdoStatement = $this->_pdoExecutor->createPDOStatement($this->_dbHandle, $this->_sql, $this->_boundValues, $sqlString);
        $result = call_user_func($afterCallback, $pdoStatement);
        $pdoStatement->closeCursor();
        return $result;
    }

    /**
     * Returns number of affected rows
     */
    public function execute()
    {
        return $this->_execute(function ($pdoStatement) {
            return $pdoStatement->rowCount();
        });
    }

    public function executeAndFetch($function, $fetchStyle)
    {
        return $this->_execute(function ($pdoStatement) use ($function, $fetchStyle) {
            return $pdoStatement->$function($fetchStyle);
        });
    }

    public function fetch($fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->executeAndFetch('fetch', $fetchMode);
    }

    public function fetchAll($fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->executeAndFetch('fetchAll', $fetchMode);
    }

    public static function prepare($dbHandle, $sql, $boundValues, $options)
    {
        $pdoExecutor = PDOExecutor::newInstance($options);
        return new StatementExecutor($dbHandle, $sql, $boundValues, $pdoExecutor);
    }
}