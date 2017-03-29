<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Logger;

use Ouzo\Config;
use Ouzo\Utilities\Arrays;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

abstract class AbstractOuzoLogger extends AbstractLogger
{
    private $_name;
    /**
     * @var MessageFormatter
     */
    private $_messageFormatter;
    private $_minimalLevels;
    private $_logger;

    public function __construct($name, $configuration)
    {
        $this->_name = $name;
        $messageFormatterClass = 'Ouzo\Logger\DefaultMessageFormatter';
        $logger = Config::getValue('logger', $configuration);
        if ($logger) {
            $messageFormatterClass = Arrays::getValue($logger, 'formatter', $messageFormatterClass);
            $this->_minimalLevels = Arrays::getValue($logger, 'minimal_levels');
        }
        $this->_messageFormatter = new $messageFormatterClass();
        $this->_logger = $logger;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    protected function logWithFunction($writeToLogFunction, $level, $message, $params)
    {
        $ouzoLevel = LogLevelTranslator::toSyslogLevel($level);
        $minimalLevel = $this->_minimalLevels ? Arrays::getValue($this->_minimalLevels, $this->_name, LOG_DEBUG) : LOG_DEBUG;
        if ($ouzoLevel <= $minimalLevel) {
            $message = $this->_messageFormatter->format($this->_name, $level, $message);
            if (!empty($params)) {
                $message = call_user_func_array('sprintf', array_merge([$message], $params));
            }
            $writeToLogFunction($message);
        }
    }

    public function debug($message, array $context = array())
    {
        if ($this->isDebug()) {
            $this->log(LogLevel::DEBUG, $message, $context);
        }
    }

    protected function isDebug()
    {
        return Config::getValue('debug');
    }

    protected function getLogger()
    {
        return $this->_logger;
    }
}
