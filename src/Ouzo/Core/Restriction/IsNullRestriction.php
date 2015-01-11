<?php
namespace Ouzo\Restriction;

class IsNullRestriction extends NoValueRestriction
{
    public function toSql($fieldName)
    {
        return $fieldName . ' IS NULL';
    }
}
