<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use PDOException;

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

        try {
            $result = $pdoStatement->execute();
        } catch (PDOException $exception) {
            throw PDOExceptionExtractor::getException($exception->getCode(), $queryString);
        }
        if (!$result) {
            throw PDOExceptionExtractor::getException($pdoStatement->errorInfo(), $queryString);
        }
        return $pdoStatement;
    }
}
