<?php
namespace Ouzo\Restriction;

class LessOrEqualToRestriction extends SingleValueRestriction
{
    public function toSql($fieldName)
    {
        return $fieldName . ' <= ?';
    }
}
