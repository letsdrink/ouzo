<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Utilities\Clock;
use Psr\Log\AbstractLogger;

class StdOutputLogger extends AbstractLogger
{
    public function __construct(string $name, string $configuration, private string $outputStreamIdentifier = 'php')
    {
    }

    public function log($level, $message, array $context = []): void
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
