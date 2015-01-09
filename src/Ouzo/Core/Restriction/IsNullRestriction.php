<?php
namespace Ouzo\Restriction;

class IsNullRestriction extends NonValueRestriction
{
    public function toSql($fieldName)
    {
        return $fieldName . ' IS NULL';
    }
}
