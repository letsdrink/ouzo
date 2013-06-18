<?php
namespace Thulium\Shell;

class Output
{
    const NL = PHP_EOL;

    private $_output;

    public function __construct($stream = 'php://stdout')
    {
        $this->_output = fopen($stream, 'w');
    }

    public function write($message, $newLines = 1)
    {
        return fwrite($this->_output, $message . str_repeat(self::NL, $newLines));
    }

    public function __destruct()
    {
        fclose($this->_output);
    }
}