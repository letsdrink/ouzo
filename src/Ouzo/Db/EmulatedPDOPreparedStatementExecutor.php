<?php

namespace Ouzo\Db;

class EmulatedPDOPreparedStatementExecutor extends PDOExecutor
{
    public function createPDOStatement($dbHandle, $sql, $boundValues, $queryString)
    {
        $sql = PreparedStatementEmulator::substitute($sql, $boundValues);

        $pdoStatement = $dbHandle->query($sql);
        if (!$pdoStatement) {
            throw PDOExceptionExtractor::getException($pdoStatement->errorInfo(), $queryString);
        }
        return $pdoStatement;
    }
} 