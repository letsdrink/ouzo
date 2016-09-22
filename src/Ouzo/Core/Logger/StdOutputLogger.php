<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Logger;

use Ouzo\Utilities\Clock;

class StdOutputLogger extends AbstractLogger
{
    private $_outputStreamIdentifier;

    public function __construct($name, $configuration, $outputStreamIdentifier = 'php')
    {
        parent::__construct($name, $configuration);
        $this->_outputStreamIdentifier = $outputStreamIdentifier;
    }

    private function _errorStreamName()
    {
        return $this->_outputStreamIdentifier . "://stderr";
    }

    private function _standardStreamName()
    {
        return $this->_outputStreamIdentifier . "://stdout";
    }

    private function _log($stdOut, $level, $levelName, $message, $params)
    {
        $this->log(function ($message) use ($stdOut) {
            $date = Clock::nowAsString();
            $fileHandle = fopen($stdOut, 'a');
            fwrite($fileHandle, "$date: $message\n");
            fclose($fileHandle);
        }, $level, $levelName, $message, $params);
    }

    public function error($message, $params = null)
    {
        $this->_log($this->_errorStreamName(), LOG_ERR, 'Error', $message, $params);
    }

    public function info($message, $params = null)
    {
        $this->_log($this->_standardStreamName(), LOG_INFO, 'Info', $message, $params);
    }

    public function debug($message, $params = null)
    {
        if ($this->isDebug()) {
            $this->_log($this->_standardStreamName(), LOG_DEBUG, 'Debug', $message, $params);
        }
    }

    public function warning($message, $params = null)
    {
        $this->_log($this->_standardStreamName(), LOG_WARNING, 'Warning', $message, $params);
    }

    public function fatal($message, $params = null)
    {
        $this->_log($this->_errorStreamName(), LOG_CRIT, 'Fatal', $message, $params);
    }
}
