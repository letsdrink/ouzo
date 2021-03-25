<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Ouzo\Db\WhereClause\WhereClause;

class JoinClause
{
    /** @var WhereClause[] $onClauses */
    public function __construct(
        public string $joinTable,
        public string $joinColumn,
        public string $joinedColumn,
        public ?string $joinedColumnTable,
        public ?string $alias = null,
        public string $type = 'LEFT',
        public ?array $onClauses = null
    )
    {
    }

    public function getJoinedColumnWithTable(): string
    {
        return "{$this->joinedColumnTable}.{$this->joinedColumn}";
    }

    public function getJoinColumnWithTable(): string
    {
        $table = $this->alias ?: $this->joinTable;
        return "{$table}.{$this->joinColumn}";
    }
}
