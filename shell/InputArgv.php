<?php
namespace Thulium\Shell;

class InputArgv
{
    private $_input;
    private $_defined;

    public function __construct(array $inputArgs, array $inputDefined)
    {
        $this->_input = $inputArgs;
        $this->_defined = $inputDefined;
    }

    public function getArgument($name)
    {
        foreach ($this->_defined as $argObj) {
            if ($argObj->checkExist($name)) {
                foreach ($this->_input as $argName => $argValues) {
                    if ($argName == $argObj->getArgNameByType($argValues['type'])) {
                        if ($this->_validateArgValue($argValues['value'], $argName, $argObj->getOption())) {
                            return $argValues['value'] ? : true;
                        }
                    }
                }
            }
        }
    }

    private function _validateArgValue($value, $arg, $option)
    {
        switch ($option) {
            case InputArgument::VALUE_NONE:
            case InputArgument::VALUE_OPTIONAL:
                return true;

            case InputArgument::VALUE_REQUIRED:
                if (!empty($value)) {
                    return true;
                }
                throw new InputArgumentException('Value for ' . $arg . ' is required');
        }
    }
}