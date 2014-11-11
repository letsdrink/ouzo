<?php

namespace Ouzo\Restriction;

class BetweenRestriction extends Restriction {

    private $value1;
    private $value2;

    function __construct($value1, $value2)
    {
        $this->value1 = $value1;
        $this->value2 = $value2;
    }

    public function toSql($fieldName)
    {
        return '(' . $fieldName . ' >= ? AND ' . $fieldName . ' <= ?)';
    }

    public function getValues()
    {
        return array($this->value1, $this->value2);
    }
}