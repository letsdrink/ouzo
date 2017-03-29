<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Logger;


class SyslogLogger extends AbstractOuzoLogger
{
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
            syslog($syslogLevel, $message);
        }, $level, $message, $context);
    }
}
