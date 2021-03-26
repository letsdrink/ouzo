<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Restriction;

class IsNotNullRestriction extends NoValueRestriction
{
    public function toSql(string $fieldName): string
    {
        return "{$fieldName} IS NOT NULL";
    }
}
