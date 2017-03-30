<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Config;
use Ouzo\Utilities\Arrays;
use Psr\Log\AbstractLogger;

abstract class AbstractOuzoLogger extends AbstractLogger
{
    private $name;
    /**
     * @var MessageFormatter
     */
    private $messageFormatter;
    private $minimalLevels;
    private $loggerConfiguration;

    public function __construct($name, $configuration)
    {
        $this->name = $name;
        $messageFormatterClass = DefaultMessageFormatter::class;
        $loggerConfiguration = Config::getValue('logger', $configuration);
        if ($loggerConfiguration) {
            $messageFormatterClass = Arrays::getValue($loggerConfiguration, 'formatter', $messageFormatterClass);
            $this->minimalLevels = Arrays::getValue($loggerConfiguration, 'minimal_levels');
        }
        $this->messageFormatter = new $messageFormatterClass();
        $this->loggerConfiguration = $loggerConfiguration;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    protected function logWithFunction($writeToLogFunction, $level, $message, $params)
    {
        $ouzoLevel = LogLevelTranslator::toSyslogLevel($level);
        $minimalLevel = $this->minimalLevels ? Arrays::getValue($this->minimalLevels, $this->name, LOG_DEBUG) : LOG_DEBUG;
        if ($ouzoLevel <= $minimalLevel) {
            $message = $this->messageFormatter->format($this->name, $level, $message);
            if (!empty($params)) {
                $message = call_user_func_array('sprintf', array_merge([$message], $params));
            }
            $writeToLogFunction($message);
        }
    }

    public function debug($message, array $context = [])
    {
        if ($this->isDebug()) {
            parent::debug($message, $context);
        }
    }

    protected function isDebug()
    {
        return Config::getValue('debug');
    }

    protected function getLoggerConfiguration()
    {
        return $this->loggerConfiguration;
    }
}
