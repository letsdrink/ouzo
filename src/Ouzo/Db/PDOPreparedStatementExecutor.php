<?php
namespace Ouzo\Db;

class PDOPreparedStatementExecutor extends PDOExecutor
{
    public function createPDOStatement($dbHandle, $sql, $boundValues, $queryString)
    {
        $pdoStatement = $dbHandle->prepare($sql);

        if (!$pdoStatement) {
            throw PDOExceptionExtractor::getException($dbHandle->errorInfo(), $queryString);
        }

        foreach ($boundValues as $key => $valueBind) {
            $type = ParameterType::getType($valueBind);
            $pdoStatement->bindValue($key + 1, $valueBind, $type);
        }

        if (!$pdoStatement->execute()) {
            throw PDOExceptionExtractor::getException($pdoStatement->errorInfo(), $queryString);
        }
        return $pdoStatement;
    }
}
