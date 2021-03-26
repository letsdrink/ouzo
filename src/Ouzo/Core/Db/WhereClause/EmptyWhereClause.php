<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\WhereClause;

class EmptyWhereClause extends WhereClause
{
    public function isEmpty(): bool
    {
        return true;
    }

    public function toSql(): string
    {
        return '';
    }

    public function getParameters(): array
    {
        return [];
    }
}
