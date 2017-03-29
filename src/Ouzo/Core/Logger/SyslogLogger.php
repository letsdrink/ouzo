<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;


class SyslogLogger extends AbstractLogger
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

    private function _log($level, $levelName, $message, $params)
    {
        $logger = $this->getLogger();
        $this->log(function ($message) use ($logger, $level) {
            if ($logger) {
                openlog($logger['ident'], $logger['option'], $logger['facility']);
            }
            $this->logMessage($level, $message);
        }, $level, $levelName, $message, $params);
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

    public function error($message, $params = null)
    {
        $this->_log(LOG_ERR, 'Error', $message, $params);
    }

    public function info($message, $params = null)
    {
        $this->_log(LOG_INFO, 'Info', $message, $params);
    }

    public function debug($message, $params = null)
    {
        if ($this->isDebug()) {
            $this->_log(LOG_DEBUG, 'Debug', $message, $params);
        }
    }

    public function warning($message, $params = null)
    {
        $this->_log(LOG_WARNING, 'Warning', $message, $params);
    }

    public function fatal($message, $params = null)
    {
        $this->_log(LOG_CRIT, 'Fatal', $message, $params);
    }
}
