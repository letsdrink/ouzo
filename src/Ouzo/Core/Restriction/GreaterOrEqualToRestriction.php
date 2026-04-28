<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Restriction;

class GreaterOrEqualToRestriction extends SingleValueRestriction
{
    #[Override]
    public function toSql(string $fieldName): string
    {
        return "{$fieldName} >= ?";
    }
}
