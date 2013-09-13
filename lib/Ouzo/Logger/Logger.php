<?php
namespace Ouzo\Logger;

use Ouzo\Config;

class Logger
{
    public static function getLogger($name)
    {
        $logger = Config::load()->getConfig('logger');
        if (!$logger) {
            return new SyslogLogger($name);
        }
        return new $logger($name);
    }
}

