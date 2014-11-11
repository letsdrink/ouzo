<?php

namespace Ouzo\Restriction;

class LikeRestriction extends Restriction {

    private $value;

    function __construct($value)
    {
        $this->value = $value;
    }

    public function toSql($fieldName)
    {
        return $fieldName . ' LIKE ?';
    }

    public function getValues()
    {
        return $this->value;
    }
}