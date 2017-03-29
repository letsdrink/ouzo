<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Logger;

use Ouzo\Utilities\Clock;

class StdOutputLogger extends AbstractOuzoLogger
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


    private function _getStreamForLogLevel($logLevel)
    {
        if (LogLevelTranslator::toSyslogLevel($logLevel) >= LOG_WARNING) {
            return $this->_standardStreamName();
        }
        return $this->_errorStreamName();
    }

    public function log($level, $message, array $context = array())
    {
        $stdOut = $this->_getStreamForLogLevel($level);
        $this->logWithFunction(function ($message) use ($stdOut) {
            $date = Clock::nowAsString();
            $fileHandle = fopen($stdOut, 'a');
            fwrite($fileHandle, "$date: $message\n");
            fclose($fileHandle);
        }, $level, $message, $context);
    }
}
