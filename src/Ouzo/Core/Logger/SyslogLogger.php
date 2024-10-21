<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Config;
use Psr\Log\AbstractLogger;

class SyslogLogger extends AbstractLogger
{
    const MAX_MESSAGE_SIZE = 1024;

    private SyslogAdapter $syslogAdapter;
    private ?array $loggerConfiguration;

    public function __construct(string $name, string $configuration, SyslogAdapter $syslogAdapter = null)
    {
        $this->syslogAdapter = $syslogAdapter ?: new SyslogAdapter();
        $this->loggerConfiguration = Config::getValue('logger', $configuration);
    }

    public function __destruct()
    {
        closelog();
    }

    public function log($level, $message, array $context = []): void
    {
        $syslogLevel = LogLevelTranslator::toSyslogLevel($level);
        if (!is_null($this->loggerConfiguration)) {
            $this->syslogAdapter->open($this->loggerConfiguration);
        }
        $this->logMessage($syslogLevel, $message);
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
