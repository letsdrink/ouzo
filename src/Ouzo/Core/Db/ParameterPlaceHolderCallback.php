<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Db;
use Ouzo\Utilities\Objects;

class ParameterPlaceHolderCallback
{
    /** @var array */
    private $boundValues = [];
    /** @var int */
    private $paramIndex = 0;
    /** @var int */
    private $quoteCount = 0;

    /**
     * @param array $boundValues
     */
    public function __construct($boundValues)
    {
        $this->boundValues = $boundValues;
    }

    /**
     * @param string $matches
     * @return string
     */
    public function __invoke($matches)
    {
        if ($matches[0] == "'") {
            $this->quoteCount++;
            return "'";
        } elseif (!$this->isInString()) {
            return $this->substituteParam();
        } else {
            return "?";
        }
    }

    /**
     * @return string|bool
     */
    private function substituteParam()
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

    /**
     * @return bool
     */
    private function isInString()
    {
        return $this->quoteCount % 2 == 1;
    }
}
