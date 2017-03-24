<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Config;
use Ouzo\Utilities\Arrays;

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
 */
class Logger
{
    private static $loggers = array();

    /**
     * @param $name
     * @param string $configuration
     * @return LoggerInterface
     */
    public static function getLogger($name, $configuration = 'default')
    {
        $logger = Arrays::getNestedValue(self::$loggers, [$name, $configuration]);
        if (!$logger) {
            $logger = self::loadLogger($name, $configuration);
            Arrays::setNestedValue(self::$loggers, [$name, $configuration], $logger);
        }
        return $logger;
    }

    private static function loadLogger($name, $configuration)
    {
        $logger = Config::getValue('logger', $configuration);
        if (!$logger || !isset($logger['class'])) {
            return new SyslogLogger($name, $configuration);
        }
        return new $logger['class']($name, $configuration);
    }
}