<?php

namespace Ouzo\Logger;

class SyslogLogProvider
{
    public function log($level, $message)
    {
        syslog($level, $message);
    }

    public function open($loggerConfiguration)
    {
        openlog($loggerConfiguration['ident'], $loggerConfiguration['option'], $loggerConfiguration['facility']);
    }
}