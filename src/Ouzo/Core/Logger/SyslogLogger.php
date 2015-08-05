<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Logger;

use Ouzo\Config;

class SyslogLogger extends AbstractLogger
{
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
            syslog($level, $message);
        }, $level, $levelName, $message, $params);
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
