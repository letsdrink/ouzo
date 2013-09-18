<?php
namespace Ouzo\Utilities;

class FluentArray
{
    private $_array;

    function __construct($_array)
    {
        $this->_array = $_array;
    }

    public static function from($_array)
    {
        return new self($_array);
    }

    public function map($function)
    {
        $this->_array = array_map($function, $this->_array);
        return $this;
    }

    public function mapKeys($function)
    {
        $this->_array = Arrays::mapKeys($this->_array, $function);
        return $this;
    }

    public function filter($function)
    {
        $this->_array = array_filter($this->_array, $function);
        return $this;
    }

    public function unique()
    {
        $this->_array = array_unique($this->_array);
        return $this;
    }

    public function keys()
    {
        $this->_array = array_keys($this->_array);
        return $this;
    }

    public function values()
    {
        $this->_array = array_values($this->_array);
        return $this;
    }

    public function flatten()
    {
        $this->_array = Arrays::flatten($this->_array);
        return $this;
    }

    public function toMap($keyFunction, $valueFunction = null)
    {
        $this->_array = Arrays::toMap($this->_array, $keyFunction, $valueFunction);
        return $this;
    }

    public function toArray()
    {
        return $this->_array;
    }

    public function toJson()
    {
        return json_encode($this->_array);
    }
}