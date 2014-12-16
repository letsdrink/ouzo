<?php
namespace Ouzo\Restriction;

class EqualToRestriction extends SingleValueRestriction
{
    public function toSql($fieldName)
    {
        return $fieldName . ' = ?';
    }
}
