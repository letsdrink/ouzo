<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Config;
use Ouzo\Utilities\Arrays;
use Psr\Log\LoggerInterface;

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
    private static array $loggers = [];

    public static function getLogger(string $name, string $configuration = 'default'): LoggerAdapter
    {
        $logger = Arrays::getNestedValue(self::$loggers, [$name, $configuration]);
        if (is_null($logger)) {
            $logger = new LoggerAdapter($name, $configuration, self::loadLogger($name, $configuration));
            Arrays::setNestedValue(self::$loggers, [$name, $configuration], $logger);
        }
        return $logger;
    }

    public static function clearLogger(string $name, string $configuration = 'default'): void
    {
        Arrays::removeNestedKey(self::$loggers, [$name, $configuration]);
    }

    private static function loadLogger(string $name, string $configuration): LoggerInterface
    {
        $logger = Config::getValue('logger', $configuration);
        if (is_null($logger) || !isset($logger['class'])) {
            return new SyslogLogger($name, $configuration);
        }
        return new $logger['class']($name, $configuration);
    }
}
