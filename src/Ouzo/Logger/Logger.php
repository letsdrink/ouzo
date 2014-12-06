<?php
namespace Ouzo\Logger;

use Ouzo\Config;

/**
 * Logger class is used to obtain reference to current logger
 * based on configuration entry $config['logger']['default']['class'].
 *
 * Sample usage:
 * <code>
 *  Logger::getLogger(__CLASS__)->debug('message');
 *  Logger::getLogger('logger name')->debug('message');
 *  Logger::getLogger('logger name', 'user_custom_config')->debug('message');
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
    private static $_configuration;

    /**
     * @param $name
     * @param string $configuration
     * @return LoggerInterface
     */
    public static function getLogger($name, $configuration = 'default')
    {
        if (!self::$_logger || self::$_configuration != $configuration) {
            self::$_logger = self::_loadLogger($name, $configuration);
        }
        self::$_configuration = $configuration;
        self::$_logger->setName($name);
        return self::$_logger;
    }

    private static function _loadLogger($name, $configuration)
    {
        $logger = Config::getValue('logger', $configuration);
        if (!$logger || !isset($logger['class'])) {
            return new SyslogLogger($name, $configuration);
        }
        return new $logger['class']($name, $configuration);
    }
}
