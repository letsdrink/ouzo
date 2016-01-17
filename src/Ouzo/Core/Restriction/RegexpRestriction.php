<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Restriction;

use Ouzo\Db\Dialect\DialectFactory;

class RegexpRestriction extends SingleValueRestriction
{
    public function toSql($fieldName)
    {
        $dialect = DialectFactory::create();
        $regexpMatcher = $dialect->regexpMatcher();
        return $fieldName . ' ' . $regexpMatcher . ' ?';
    }
}
