<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Utilities\Clock;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class StdOutputLogger implements LoggerInterface
{
    public function __construct(string $name, string $configuration, private string $outputStreamIdentifier = 'php')
    {
    }

    public function log($level, $message, array $context = []): void
    {
        $this->doLog($level, $message);
    }

    public function emergency($message, array $context = array())
    {
        $this->doLog(LogLevel::EMERGENCY, $message);
    }

    public function alert($message, array $context = array())
    {
        $this->doLog(LogLevel::ALERT, $message);
    }

    public function critical($message, array $context = array())
    {
        $this->doLog(LogLevel::CRITICAL, $message);
    }

    public function error($message, array $context = array())
    {
        $this->doLog(LogLevel::ERROR, $message);
    }

    public function warning($message, array $context = array())
    {
        $this->doLog(LogLevel::WARNING, $message);
    }

    public function notice($message, array $context = array())
    {
        $this->doLog(LogLevel::NOTICE, $message);
    }

    public function info($message, array $context = array())
    {
        $this->doLog(LogLevel::INFO, $message);
    }

    public function debug($message, array $context = array())
    {
        $this->doLog(LogLevel::DEBUG, $message);
    }

    private function doLog(mixed $level, string $message): void
    {
        $stdOut = $this->getStreamForLogLevel($level);
        $date = Clock::nowAsString();
        $fileHandle = fopen($stdOut, 'a');
        fwrite($fileHandle, "$date: $message\n");
        fclose($fileHandle);
    }

    private function errorStreamName(): string
    {
        return "{$this->outputStreamIdentifier}://stderr";
    }

    private function standardStreamName(): string
    {
        return "{$this->outputStreamIdentifier}://stdout";
    }

    private function getStreamForLogLevel(string $logLevel): string
    {
        if (LogLevelTranslator::toSyslogLevel($logLevel) >= LOG_WARNING) {
            return $this->standardStreamName();
        }
        return $this->errorStreamName();
    }
}
