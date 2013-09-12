<?php
namespace Ouzo\Shell;

class InputArgument
{
    const VALUE_NONE = 1;
    const VALUE_REQUIRED = 2;
    const VALUE_OPTIONAL = 3;
    private $_longName;
    private $_shortName;
    private $_option;

    public function __construct($longName, $shortName, $option)
    {
        $this->_longName = $longName;
        $this->_shortName = $shortName;
        $this->_option = $option;
    }

    public function getLongName()
    {
        return $this->_longName;
    }

    public function isLongName($param)
    {
        return $param == $this->getLongName();
    }

    public function getShortName()
    {
        return $this->_shortName;
    }

    public function isShortName($param)
    {
        return $param == $this->getShortName();
    }

    public function getOption()
    {
        return $this->_option;
    }

    public function getArgNameByType($type)
    {
        return $type == 'long' ? $this->getLongName() : $this->getShortName();
    }

    public function checkExist($argName)
    {
        return $this->isLongName($argName) || $this->isShortName($argName);
    }
}

class InputArgumentException extends \Exception
{
}