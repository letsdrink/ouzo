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
    private $logger;

    public function __construct($name, $configuration)
    {
        $this->name = $name;
        $messageFormatterClass = 'Ouzo\Logger\DefaultMessageFormatter';
        $logger = Config::getValue('logger', $configuration);
        if ($logger) {
            $messageFormatterClass = Arrays::getValue($logger, 'formatter', $messageFormatterClass);
            $this->minimalLevels = Arrays::getValue($logger, 'minimal_levels');
        }
        $this->messageFormatter = new $messageFormatterClass();
        $this->logger = $logger;
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

    public function debug($message, array $context = array())
    {
        if ($this->isDebug()) {
            parent::debug($message, $context);
        }
    }

    protected function isDebug()
    {
        return Config::getValue('debug');
    }

    protected function getLogger()
    {
        return $this->logger;
    }
}
