<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Restriction;

class IsNotInRestriction extends Restriction
{
    public function __construct(private mixed $values)
    {
    }

    public function toSql(string $fieldName): string
    {
        if (!count($this->values) > 0) {
            return '';
        }
        $placeholders = implode(', ', array_fill(0, count($this->values), '?'));
        return "{$fieldName} NOT IN({$placeholders})";
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
