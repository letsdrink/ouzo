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
    private $_boundValues = array();
    private $_param_index = 0;
    private $_quote_count = 0;

    public function __construct($boundValues)
    {
        $this->_boundValues = $boundValues;
    }

    public function __invoke($matches)
    {
        if ($matches[0] == "'") {
            $this->_quote_count++;
            return "'";
        } elseif (!$this->isInString()) {
            return $this->substituteParam();
        } else {
            return "?";
        }
    }

    private function substituteParam()
    {
        $value = $this->_boundValues[$this->_param_index];
        $this->_param_index++;
        if ($value === null) {
            return "null";
        }
        if (is_bool($value)) {
            return Objects::booleanToString($value);
        }
        $value = Db::getInstance()->_dbHandle->quote($value);
        return $value;
    }

    private function isInString()
    {
        return $this->_quote_count % 2 == 1;
    }
}

class PreparedStatementEmulator
{
    public static function substitute($sql, $params)
    {
        return preg_replace_callback('/[\'?]/', new ParameterPlaceHolderCallback($params), $sql);
    }
}
