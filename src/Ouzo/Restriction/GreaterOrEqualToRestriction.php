<?php

namespace Ouzo\Restriction;

class GreaterOrEqualToRestriction extends SingleValueRestriction {

    public function toSql($fieldName)
    {
        return $fieldName . ' >= ?';
    }
}