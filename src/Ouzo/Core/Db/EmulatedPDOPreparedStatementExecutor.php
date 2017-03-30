<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class EmulatedPDOPreparedStatementExecutor extends PDOExecutor
{
    /**
     * @inheritdoc
     */
    public function createPDOStatement($dbHandle, $sql, $boundValues, $queryString)
    {
        $sql = PreparedStatementEmulator::substitute($sql, $boundValues);

        $pdoStatement = $dbHandle->query($sql);
        if (!$pdoStatement) {
            throw PDOExceptionExtractor::getException($dbHandle->errorInfo(), $queryString);
        }
        return $pdoStatement;
    }
}
