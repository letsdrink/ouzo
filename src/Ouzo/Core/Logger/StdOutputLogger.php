<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Utilities\Clock;

class StdOutputLogger extends AbstractOuzoLogger
{
    public function __construct(string $name, string $configuration, private string $outputStreamIdentifier = 'php')
    {
        parent::__construct($name, $configuration);
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

    public function log($level, $message, array $context = [])
    {
        $stdOut = $this->getStreamForLogLevel($level);
        $this->logWithFunction(function ($message) use ($stdOut) {
            $date = Clock::nowAsString();
            $fileHandle = fopen($stdOut, 'a');
            fwrite($fileHandle, "$date: $message\n");
            fclose($fileHandle);
        }, $level, $message, $context);
    }
}
