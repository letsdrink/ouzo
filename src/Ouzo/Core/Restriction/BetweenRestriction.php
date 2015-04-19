<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Restriction;

class BetweenRestriction extends Restriction
{
    private $value1;
    private $value2;
    private $betweenMode;

    public function __construct($value1, $value2, $betweenMode)
    {
        $this->value1 = $value1;
        $this->value2 = $value2;
        $this->betweenMode = $betweenMode;
    }

    public function toSql($fieldName)
    {
        switch ($this->betweenMode) {
            case Between::EXCLUSIVE:
                return '(' . $fieldName . ' > ? AND ' . $fieldName . ' < ?)';
            case Between::LEFT_EXCLUSIVE:
                return '(' . $fieldName . ' > ? AND ' . $fieldName . ' <= ?)';
            case Between::RIGHT_EXCLUSIVE:
                return '(' . $fieldName . ' >= ? AND ' . $fieldName . ' < ?)';
        }
        return '(' . $fieldName . ' >= ? AND ' . $fieldName . ' <= ?)';
    }

    public function getValues()
    {
        return array($this->value1, $this->value2);
    }
}
