<?php
namespace Ouzo\Restriction;

class LessThanRestriction extends SingleValueRestriction
{
    public function toSql($fieldName)
    {
        return $fieldName . ' < ?';
    }
}
