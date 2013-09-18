<?php
namespace Ouzo\Logger;

use Ouzo\Config;

/**
 * Logger class is used to obtain reference to current logger
 * based on configuration entry $config['logger']['class'].
 *
 * Sample usage:
 * <code>
 *  Logger::getLogger(__CLASS__)->debug('message');
 *  Logger::getLogger('logger name')->debug('message');
 * </code>
 *
 * However, this won't work correctly:
 * <code>
 *  $loggerA = Logger::getLogger('A');
 *  $loggerB = Logger::getLogger('B');
 *  $loggerA->debug('message');
 * </code>
 */
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
        self::$_logger->setName($name);
        return self::$_logger;
    }

    private static function _loadLogger($name)
    {
        $logger = Config::getValue('logger');
        if (!$logger || !isset($logger['class'])) {
            return new SyslogLogger($name);
        }
        return new $logger['class']($name);
    }
}

