<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\WhereClause;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class SqlWhereClause extends WhereClause
{
    private string $sql;
    private array $values;

    public function __construct(string $sql, null|array|string $parameters = [])
    {
        $this->sql = $sql;
        $this->values = $parameters === null ? [null] : Arrays::toArray($parameters);
    }

    public function toSql(): string
    {
        return stripos($this->sql, 'OR') ? "({$this->sql})" : $this->sql;
    }

    public function getParameters(): array
    {
        return $this->values;
    }

    public function isEmpty(): bool
    {
        return Strings::isBlank($this->sql);
    }
}
