<?php

namespace Ouzo\Restriction;

class ILikeRestriction extends SingleValueRestriction {

    public function toSql($fieldName)
    {
        return $fieldName . ' ILIKE ?';
    }
}