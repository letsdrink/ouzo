<?php
namespace Ouzo\Restriction;

class IsNotNullRestriction extends NonValueRestriction
{
    public function toSql($fieldName)
    {
        return $fieldName . ' IS NOT NULL';
    }
}
