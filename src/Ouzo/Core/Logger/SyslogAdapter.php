<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

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
