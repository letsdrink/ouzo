<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\Dialect;

use InvalidArgumentException;
use Ouzo\Db\QueryType;
use Ouzo\Utilities\Arrays;

class MySqlDialect extends Dialect
{
    /**
     * @inheritdoc
     */
    public function table()
    {
        $alias = $this->query->aliasTable;
        $table = $this->tableOrSubQuery();
        if ($alias) {
            $aliasOperator = $this->query->type == QueryType::$DELETE ? '' : ' AS ';
            return $table . $aliasOperator . $alias;
        }
        return $table;
    }

    /**
     * @inheritdoc
     */
    public function getConnectionErrorCodes()
    {
        return [2003, 2006];
    }

    /**
     * @inheritdoc
     */
    public function getErrorCode($errorInfo)
    {
        return Arrays::getValue($errorInfo, 1);
    }

    /**
     * @inheritdoc
     */
    public function using()
    {
        return $this->_using($this->query->usingClauses, ' INNER JOIN ', $this->query->table, $this->query->aliasTable);
    }

    /**
     * @inheritdoc
     */
    public function batchInsert($table, $primaryKey, $columns, $batchSize)
    {
        throw new InvalidArgumentException("Batch insert not supported in mysql");
    }

    /**
     * @inheritdoc
     */
    protected function insertEmptyRow()
    {
        return "INSERT INTO {$this->query->table} VALUES ()";
    }

    /**
     * @inheritdoc
     */
    public function regexpMatcher()
    {
        return 'REGEXP';
    }

    /**
     * @inheritdoc
     */
    protected function quote($word)
    {
        return '`' . $word . '`';
    }

    /**
     * @inheritdoc
     */
    public function onConflictUpdate()
    {
        $attributes = DialectUtil::buildAttributesPartForUpdate($this->query->updateAttributes);
        return " ON DUPLICATE KEY UPDATE {$attributes}";
    }
}
