<?php

namespace Ouzo\Logger;

class SyslogAdapter
{
    public function log(string $level, string $message): void
    {
        syslog($level, $message);
    }

    public function open(array $loggerConfiguration): void
    {
        openlog($loggerConfiguration['ident'], $loggerConfiguration['option'], $loggerConfiguration['facility']);
    }
}
