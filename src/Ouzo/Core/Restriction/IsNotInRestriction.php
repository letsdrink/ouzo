<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Restriction;


class IsNotInRestriction extends Restriction
{
    private $values;

    function __construct($values)
    {
        $this->values = $values;
    }

    public function toSql($fieldName)
    {
        if (!count($this->values) > 0) {
            return null;
        }
        $placeholders = implode(', ', array_fill(0, count($this->values), '?'));
        return $fieldName . ' NOT IN(' . $placeholders . ')';
    }

    public function getValues()
    {
        return $this->values;
    }
}