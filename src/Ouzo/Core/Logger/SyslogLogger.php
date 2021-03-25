<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Config;

class SyslogLogger extends AbstractOuzoLogger
{
    const MAX_MESSAGE_SIZE = 1024;

    private SyslogAdapter $syslogAdapter;

    public function __construct($name, $configuration, SyslogAdapter $syslogAdapter = null)
    {
        parent::__construct($name, $configuration);
        $this->syslogAdapter = $syslogAdapter ?: new SyslogAdapter();
    }

    public function __destruct()
    {
        closelog();
    }

    public function log($level, $message, array $context = [])
    {
        $loggerConfiguration = $this->getLoggerConfiguration();
        $syslogLevel = LogLevelTranslator::toSyslogLevel($level);
        $this->logWithFunction(function ($message) use ($loggerConfiguration, $syslogLevel) {
            if ($loggerConfiguration) {
                $this->syslogAdapter->open($loggerConfiguration);
            }
            $this->logMessage($syslogLevel, $message);
        }, $level, $message, $context);
    }

    private function logMessage(string $level, string $message): void
    {
        $messageLength = strlen($message);
        if ($messageLength < $this->getMaxMessageSize()) {
            $this->syslogAdapter->log($level, $message);
        } else {
            $messageId = uniqid();
            $multipartMessagePrefix = "Multipart {$messageId} [%d/%d] ";

            $parts = str_split($message, $this->getMaxMessageSize() - strlen($multipartMessagePrefix) - 10);
            foreach ($parts as $idx => $part) {
                $prefix = sprintf($multipartMessagePrefix, $idx + 1, sizeof($parts));
                $this->syslogAdapter->log($level, $prefix . $part);
            }
        }
    }

    private function getMaxMessageSize(): int
    {
        return Config::getValue('logger', 'syslog', 'max_message_size') ?: self::MAX_MESSAGE_SIZE;
    }
}
