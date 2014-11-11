<?php

namespace Ouzo\Restriction;

class ILikeRestriction extends Restriction {

    private $value;

    function __construct($value)
    {
        $this->value = $value;
    }

    public function toSql($fieldName)
    {
        return $fieldName . ' ILIKE ?';
    }

    public function getValues()
    {
        return $this->value;
    }
}