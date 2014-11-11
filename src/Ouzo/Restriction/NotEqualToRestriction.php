<?php

namespace Ouzo\Restriction;

class NotEqualToRestriction extends SingleValueRestriction {

    public function toSql($fieldName)
    {
        return $fieldName . ' <> ?';
    }
}