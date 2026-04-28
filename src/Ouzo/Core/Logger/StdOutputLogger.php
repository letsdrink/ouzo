<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Utilities\Clock;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

class StdOutputLogger implements LoggerInterface
{
    public function __construct(string $name, string $configuration, private string $outputStreamIdentifier = 'php')
    {
    }

    #[Override]
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->doLog($level, (string)$message);
    }

    #[Override]
    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::EMERGENCY, (string)$message);
    }

    #[Override]
    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::ALERT, (string)$message);
    }

    #[Override]
    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::CRITICAL, (string)$message);
    }

    #[Override]
    public function error(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::ERROR, (string)$message);
    }

    #[Override]
    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::WARNING, (string)$message);
    }

    #[Override]
    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::NOTICE, (string)$message);
    }

    #[Override]
    public function info(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::INFO, (string)$message);
    }

    #[Override]
    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::DEBUG, (string)$message);
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
