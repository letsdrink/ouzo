<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Restriction;

class IsNullRestriction extends NoValueRestriction
{
    public function toSql(string $fieldName): string
    {
        return "{$fieldName} IS NULL";
    }
}
