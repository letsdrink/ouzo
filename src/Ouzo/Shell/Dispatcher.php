<?php
namespace Ouzo\Shell;

use Ouzo\Shell;
use Ouzo\UserException;

class Dispatcher
{
    public $args = array();
    public $params = array();

    public static function runScript($argv)
    {
        try {
            $dispatcher = new Dispatcher($argv);
            $dispatcher->dispatch();
        } catch (UserException $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    public function __construct($argv)
    {
        $this->parseArgs($argv);
        $this->_bootstrap();
    }

    public function dispatch()
    {
        $this->_argsShift();

        $appName = $this->_getApplicationName();

        $shellScript = $this->_getScriptShell($appName);

        if ($shellScript instanceof Shell) {
            $method = empty($this->params['method']) ? '' : $this->params['method'];
            $argv = $this->params['argv'];
            $shellScript->runCommand($method, $argv);
        } else {
            throw new DispatcherAppDoesNotExistException(sprintf("Class '%s' is not shell application.", $this->_getAppClassName($appName)));
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
        defined('APP') or define('APP', $this->params['app']);
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

    private function _getScriptShell($applicationName)
    {
        if (empty($applicationName)) {
            throw new DispatcherAppException("Empty application name.");
        }
        $className = $this->_getAppClassName($applicationName);

        if (!class_exists($className)) {
            throw new DispatcherAppException(sprintf("Class '%s' for application '%s' does not exist.", $className, $applicationName));
        }

        $class = new $className();
        return $class;
    }

    private function _getApplicationName()
    {
        return $this->params['app'];
    }

    private function _getAppClassName($applicationName)
    {
        return $applicationName . 'Shell';
    }
}