<?php
namespace Ouzo\Restriction;

class GreaterThanRestriction extends SingleValueRestriction
{
    public function toSql($fieldName)
    {
        return $fieldName . ' > ?';
    }
}
