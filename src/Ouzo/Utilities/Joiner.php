<?php
namespace Ouzo\Utilities;

class Joiner
{
    private $_separator;
    private $_skipNulls;
    private $_function;
    private $_valuesFunction;

    public function __construct($separator)
    {
        $this->_separator = $separator;
    }

    /**
     * @param $separator
     * @return Joiner
     */
    public static function on($separator)
    {
        return new Joiner($separator);
    }

    public function join(array $array)
    {
        $function = $this->_function;
        $valuesFunction = $this->_valuesFunction;
        $result = '';
        foreach ($array as $key => $value) {
            if (!$this->_skipNulls || ($this->_skipNulls && $value)) {
                $result .= (
                    $function ? $function($key, $value) :
                        ($valuesFunction ? $valuesFunction($value) : $value)
                    ) . $this->_separator;
            }
        }
        return rtrim($result, $this->_separator);
    }

    /**
     * @return Joiner
     */
    public function skipNulls()
    {
        $this->_skipNulls = true;
        return $this;
    }

    public function map($function)
    {
        $this->_function = $function;
        return $this;
    }

    public function mapValues($function)
    {
        $this->_valuesFunction = $function;
        return $this;
    }
}