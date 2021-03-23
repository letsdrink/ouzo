<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use PDO;
use PDOStatement;

class EmulatedPDOPreparedStatementExecutor extends PDOExecutor
{
    public function createPDOStatement(PDO $dbHandle, string $sql, array $boundValues, string $queryString, array $options = []): PDOStatement
    {
        $sql = PreparedStatementEmulator::substitute($sql, $boundValues);

        $pdoStatement = $dbHandle->query($sql);
        if (!$pdoStatement) {
            throw PDOExceptionExtractor::getException($dbHandle->errorInfo(), $queryString);
        }
        return $pdoStatement;
    }
}
