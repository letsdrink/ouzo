<?php

namespace Ouzo\Logger;

use Ouzo\Config;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class StdOutputLogger implements LoggerInterface
{
    private $_name;
    /**
     * @var MessageFormatter
     */
    private $_messageFormatter;
    private $_outputStreamIdentifier;

    public function __construct($name, $outputStreamIdentifier = 'php')
    {
        $this->_name = $name;
        $messageFormatterClass = 'Ouzo\Logger\DefaultMessageFormatter';
        $logger = Config::getValue('logger');
        if ($logger) {
            $messageFormatterClass = Arrays::getValue($logger, 'formatter', $messageFormatterClass);
        }
        $this->_messageFormatter = new $messageFormatterClass();
        $this->_outputStreamIdentifier = $outputStreamIdentifier;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    private function _errorStreamName()
    {
        return $this->_outputStreamIdentifier . "://stderr";
    }

    private function _standardStreamName()
    {
        return $this->_outputStreamIdentifier . "://stdout";
    }

    private function log($stdOut, $levelName, $message, $params)
    {
        $message = $this->_messageFormatter->format($this->_name, $levelName, $message);
        if (!empty($params)) {
            $message = call_user_func_array('sprintf', array_merge(array($message), $params));
        }
        $date = Clock::nowAsString();
        $fileHandle = fopen($stdOut, 'a');
        fwrite($fileHandle, "$date: $message\n");
        fclose($fileHandle);
    }

    public function error($message, $params = null)
    {
        $this->log($this->_errorStreamName(), 'Error', $message, $params);
    }

    public function info($message, $params = null)
    {
        $this->log($this->_standardStreamName(), 'Info', $message, $params);
    }

    public function debug($message, $params = null)
    {
        if ($this->_isDebug()) {
            $this->log($this->_standardStreamName(), 'Debug', $message, $params);
        }
    }

    private function _isDebug()
    {
        return Config::getValue('debug');
    }

    public function warning($message, $params = null)
    {
        $this->log($this->_standardStreamName(), 'Warning', $message, $params);
    }

    public function fatal($message, $params = null)
    {
        $this->log($this->_errorStreamName(), 'Fatal', $message, $params);
    }
}