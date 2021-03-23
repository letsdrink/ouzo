<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use PDO;
use PDOException;
use PDOStatement;

class PDOPreparedStatementExecutor extends PDOExecutor
{
    public function createPDOStatement(PDO $dbHandle, string $sql, array $boundValues, string $queryString, array $options = []): PDOStatement
    {
        try {
            $pdoStatement = $dbHandle->prepare($sql, $options);

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
        } catch (PDOException $exception) {
            $errorInfo = [$exception->getCode(), $exception->getCode(), $exception->getMessage()];
            throw PDOExceptionExtractor::getException($errorInfo, $queryString);
        }
        return $pdoStatement;
    }
}
