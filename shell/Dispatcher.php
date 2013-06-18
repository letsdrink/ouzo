<?php
namespace Thulium\Shell;

use Thulium\Shell;

class Dispatcher
{
    public $args = array();
    public $params = array();

    static public function runScript($argv)
    {
        $dispatcher = new Dispatcher($argv);
        $dispatcher->dispatch();
    }

    public function __construct($argv)
    {
        $this->parseArgs($argv);
        $this->_bootstrap();
    }

    public function dispatch()
    {
        $this->_argsShift();

        $shellScript = $this->_getScriptShell();

        if ($shellScript instanceof Shell) {
            $method = empty($this->params['method']) ? '' : $this->params['method'];
            $argv = $this->params['argv'];
            $shellScript->runCommand($method, $argv);
        }
    }

    public function parseArgs($args)
    {
        $this->args = $args;

        $this->params['script'] = array_shift($this->args);
        $this->params['app'] = array_shift($this->args);
        $this->params['argv'] = array();

        foreach ($this->args as $arg) {
            if (preg_match('/^-[^-]/', $arg)) {
                $this->params['argv'] = array_merge($this->_extractArgs($arg, 'short'), $this->params['argv']);
            } elseif (preg_match('/^--[^-]/', $arg)) {
                $this->params['argv'] = array_merge($this->_extractArgs($arg, 'long'), $this->params['argv']);
            } else {
                if (empty($this->params['method'])) {
                    $this->params['method'] = $arg;
                }
            }
        }
    }

    protected function _bootstrap()
    {
        define('APP', $this->params['app']);
    }

    private function _extractArgs($arg, $type)
    {
        $extractedArg = array();

        if (strpos($arg, '=') !== false) {
            $partsString = explode('=', $arg);

            $paramName = str_replace('-', '', $partsString[0]);
            $paramValue = $partsString[1];

            $extractedArg[$paramName]['type'] = $type;
            $extractedArg[$paramName]['value'] = $paramValue;
        } else {
            $paramName = str_replace('-', '', $arg);

            $extractedArg[$paramName]['type'] = $type;
            $extractedArg[$paramName]['value'] = '';
        }

        return $extractedArg;
    }

    private function _argsShift()
    {
        return array_shift($this->args);
    }

    private function _getScriptShell($name = '')
    {
        if (empty($name)) {
            $className = $this->params['app'] . 'Shell';
        }

        $class = new $className();

        return $class;
    }
}