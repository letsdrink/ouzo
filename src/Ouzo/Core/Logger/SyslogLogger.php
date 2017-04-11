<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Config;

class SyslogLogger extends AbstractOuzoLogger
{
    const MAX_MESSAGE_SIZE = 1024;

    /**
     * @var SyslogLogProvider
     */
    private $syslogLogProvider;

    public function __construct($name, $configuration, $syslogLogProvider = null)
    {
        parent::__construct($name, $configuration);
        $this->syslogLogProvider = $syslogLogProvider ?: new SyslogLogProvider();
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
                $this->syslogLogProvider->open($loggerConfiguration);
            }
            $this->logMessage($syslogLevel, $message);
        }, $level, $message, $context);
    }

    private function logMessage($level, $message)
    {
        $messageLength = strlen($message);
        if ($messageLength < $this->getMaxMessageSize()) {
            $this->syslogLogProvider->log($level, $message);
        } else {
            $messageId = uniqid();
            $multipartMessagePrefix = "Multipart $messageId [%d/%d] ";

            $parts = str_split($message, $this->getMaxMessageSize() - strlen($multipartMessagePrefix) - 10);
            foreach ($parts as $idx => $part) {
                $prefix = sprintf($multipartMessagePrefix, $idx + 1, sizeof($parts));
                $this->syslogLogProvider->log($level, $prefix . $part);
            }
        }
    }

    private function getMaxMessageSize()
    {
        return Config::getValue('logger', 'syslog', 'max_message_size') ?: self::MAX_MESSAGE_SIZE;
    }
}
