<?php
namespace Ouzo;

use Ouzo\Shell\InputArgument;
use Ouzo\Shell\InputArgv;
use Ouzo\Shell\Output;

class Shell
{
    public $stdout;
    private $_arguments;
    private $_inputDefinition;

    public function __construct()
    {
        $this->stdout = new Output('php://stdout');
    }

    public function out($message = '', $newLines = 1)
    {
        return $this->stdout->write($message, $newLines);
    }

    public function hr($width = 63)
    {
        $this->out(str_repeat('-', $width));
    }

    public function progressBar($number, $limit, $message = '')
    {
        $width = 50;
        $percent = $number / $limit;
        $fill = round($percent * $width, 0);
        $string = '[' . str_repeat('=', $fill) . str_repeat(' ', $width - $fill) . ']';
        $string .= ' ' . $number . '/' . $limit;
        $string .= $message;
        return $this->stdout->overwrite($string);
    }

    public function runCommand($method, $args)
    {
        $isConfig = $this->hasMethod('configure');
        $isMain = $this->hasMethod('main');
        $isValidClassMethod = $this->hasMethod($method);

        if ($isMain || $isValidClassMethod) {
            $this->startup();
        }

        if ($isConfig) {
            $this->configure();
        }

        if (!empty($args) && !empty($this->_arguments)) {
            $this->_inputDefinition = new InputArgv($args, $this->_arguments);
        }

        if ($isMain) {
            $this->main();
        }

        if ($isValidClassMethod) {
            $this->$method();
        }
    }

    public function hasMethod($name)
    {
        try {
            $method = new \ReflectionMethod($this, $name);
            return $method->isPublic();
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    public function startup($info = null)
    {
        if (empty($info)) {
            $this->_thuliumInfo();
        }
    }

    public function addArgument($longName, $shortName, $option)
    {
        $this->_arguments[] = new InputArgument($longName, $shortName, $option);
        return $this;
    }

    public function getArgument($name)
    {
        return $this->_inputDefinition ? $this->_inputDefinition->getArgument($name) : null;
    }

    public function main()
    {
    }

    public function configure()
    {
    }

    private function _thuliumInfo()
    {
        $this->hr();
        $this->out("Ouzo Framework Console");
        $this->hr();
        $this->out("App : " . APP);
        $this->hr();
    }
}