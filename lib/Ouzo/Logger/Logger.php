<?php
namespace Ouzo\Logger;

use Ouzo\Config;

class Logger
{
    private static $_logger;

    /**
     * @return LoggerInterface
     */
    public static function getLogger($name)
    {
        if (!self::$_logger) {
            self::$_logger = self::_loadLogger($name);
        }
        return self::$_logger;
    }

    private static function _loadLogger($name)
    {
        $logger = Config::load()->getConfig('logger');
        if (!$logger || !isset($logger['class'])) {
            return new SyslogLogger($name);
        }
        return new $logger['class']($name);
    }
}

