<?php

namespace Ouzo\Restriction;

class LikeRestriction extends SingleValueRestriction {

    public function toSql($fieldName)
    {
        return $fieldName . ' LIKE ?';
    }
}