<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Restriction;

class BetweenRestriction extends Restriction
{
    public function __construct(
        private mixed $value1,
        private mixed $value2,
        private int $betweenMode)
    {
    }

    public function toSql(string $fieldName): string
    {
        return match ($this->betweenMode) {
            Between::EXCLUSIVE => "({$fieldName} > ? AND {$fieldName} < ?)",
            Between::LEFT_EXCLUSIVE => "({$fieldName} > ? AND {$fieldName} <= ?)",
            Between::RIGHT_EXCLUSIVE => "({$fieldName} >= ? AND {$fieldName} < ?)",
            default => "({$fieldName} >= ? AND {$fieldName} <= ?)"
        };
    }

    public function getValues(): array
    {
        return [$this->value1, $this->value2];
    }
}
