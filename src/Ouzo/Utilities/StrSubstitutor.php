<?php
namespace Ouzo\Utilities;

class StrSubstitutor
{
    private $_values;
    private $_default;

    public function __construct($values = array(), $default = null)
    {
        $this->_values = $values;
        $this->_default = $default;
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
        $default = $this->_default ?: $matched;
        return isset($this->_values[$name]) ? $this->_values[$name] : $default;
    }
}