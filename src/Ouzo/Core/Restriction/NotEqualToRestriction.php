<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Restriction;

class NotEqualToRestriction extends SingleValueRestriction
{
    public function toSql($fieldName)
    {
        return $fieldName . ' <> ?';
    }
}
