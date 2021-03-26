<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Restriction;

use Ouzo\Db\Dialect\DialectFactory;

class RegexpRestriction extends SingleValueRestriction
{
    public function toSql(string $fieldName): string
    {
        $dialect = DialectFactory::create();
        $regexpMatcher = $dialect->regexpMatcher();
        return "{$fieldName} {$regexpMatcher} ?";
    }
}
