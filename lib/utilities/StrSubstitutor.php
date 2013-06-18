<?php

namespace Thulium\Utilities;


class StrSubstitutor
{
    private $_values;

    public function __construct($values = array())
    {
        $this->_values = $values;
    }

    public function replace($string)
    {
        return preg_replace_callback('/\{\{(\w+)}}/', array($this, '_replace_vars'), $string);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function _replace_vars($match)
    {
        $matched = $match[0];
        $name = $match[1];
        return isset($this->_values[$name]) ? $this->_values[$name] : $matched;
    }
}