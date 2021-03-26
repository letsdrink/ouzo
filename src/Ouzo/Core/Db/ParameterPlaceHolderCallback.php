<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Ouzo\Db;
use Ouzo\Utilities\Objects;

class ParameterPlaceHolderCallback
{
    private array $boundValues = [];
    private int $paramIndex = 0;
    private int $quoteCount = 0;

    public function __construct(array $boundValues)
    {
        $this->boundValues = $boundValues;
    }

    public function __invoke(array $matches): string
    {
        if ($matches[0] == "'") {
            $this->quoteCount++;
            return "'";
        }
        if (!$this->isInString()) {
            return $this->substituteParam();
        }
        return "?";
    }

    private function substituteParam(): string
    {
        $value = $this->boundValues[$this->paramIndex];
        $this->paramIndex++;
        if ($value === null) {
            return "null";
        }
        if (is_bool($value)) {
            return Objects::booleanToString($value);
        }
        return Db::getInstance()->dbHandle->quote($value);
    }

    private function isInString(): bool
    {
        return $this->quoteCount % 2 === 1;
    }
}
