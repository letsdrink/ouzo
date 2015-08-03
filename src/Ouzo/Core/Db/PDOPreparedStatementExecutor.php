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
            if (!$pdoStatement->execute()) {
                throw PDOExceptionExtractor::getException($pdoStatement->errorInfo(), $queryString);
            }
        } catch (PDOException $exception) {
            $errorInfo = array($exception->getCode(), $exception->getCode(), $exception->getMessage());
            throw PDOExceptionExtractor::getException($errorInfo, $queryString);
        }
        return $pdoStatement;
    }
}
