<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Config;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

class SyslogLogger implements LoggerInterface
{
    const MAX_MESSAGE_SIZE = 1024;

    private SyslogAdapter $syslogAdapter;
    private ?array $loggerConfiguration;

    public function __construct(string $name, string $configuration, ?SyslogAdapter $syslogAdapter = null)
    {
        $this->syslogAdapter = $syslogAdapter ?: new SyslogAdapter();
        $this->loggerConfiguration = Config::getValue('logger', $configuration);
    }

    public function __destruct()
    {
        closelog();
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->doLog($level, (string)$message);
    }

    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::EMERGENCY, (string)$message);
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::ALERT, (string)$message);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::CRITICAL, (string)$message);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::ERROR, (string)$message);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::WARNING, (string)$message);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::NOTICE, (string)$message);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::INFO, (string)$message);
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->doLog(LogLevel::DEBUG, (string)$message);
    }

    private function doLog(mixed $level, string $message): void
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
