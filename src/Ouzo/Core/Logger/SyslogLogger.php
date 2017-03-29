<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Logger;


class SyslogLogger extends AbstractOuzoLogger
{
    const MAX_MESSAGE_SIZE = 1024;

    public function __construct($name, $configuration)
    {
        parent::__construct($name, $configuration);
    }

    public function __destruct()
    {
        closelog();
    }

    public function log($level, $message, array $context = array())
    {
        $logger = $this->getLogger();
        $syslogLevel = LogLevelTranslator::toSyslogLevel($level);
        $this->logWithFunction(function ($message) use ($logger, $syslogLevel) {
            if ($logger) {
                openlog($logger['ident'], $logger['option'], $logger['facility']);
            }
            $this->logMessage($syslogLevel, $message);
        }, $level, $message, $context);
    }

    private function logMessage($level, $message)
    {
        $messageLength = strlen($message);
        if ($messageLength < self::MAX_MESSAGE_SIZE) {
            syslog($level, $message);
        } else {
            $messageId = uniqid();
            $multipartMessagePrefix = "Multipart $messageId [%d/%d] ";

            $parts = str_split($message, self::MAX_MESSAGE_SIZE - strlen($multipartMessagePrefix) - 10);
            foreach ($parts as $idx => $part) {
                $prefix = sprintf($multipartMessagePrefix, $idx + 1, sizeof($parts));
                syslog($level, $prefix . $part);
            }
        }
    }
}
