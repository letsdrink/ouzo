<?php
namespace Ouzo\Restriction;

class IsNotNullRestriction extends NoValueRestriction
{
    public function toSql($fieldName)
    {
        return $fieldName . ' IS NOT NULL';
    }
}
