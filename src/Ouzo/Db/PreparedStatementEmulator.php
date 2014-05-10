<?php
namespace Ouzo\Db;

use Ouzo\Db;
use Ouzo\Utilities\Objects;

class ParameterPlaceHolderCallback
{
    private $_boundValues = array();
    private $_param_index = 0;
    private $_quote_count = 0;

    function __construct($boundValues)
    {
        $this->_boundValues = $boundValues;
    }

    function __invoke($matches)
    {
        if ($matches[0] == "'") {
            $this->_quote_count++;
            return "'";
        } else if (!$this->isInString()) {
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

class PreparedStatementEmulator {

    public static function substitute($sql, $params)
    {
        return preg_replace_callback('/[\'?]/', new ParameterPlaceHolderCallback($params), $sql);
    }
}