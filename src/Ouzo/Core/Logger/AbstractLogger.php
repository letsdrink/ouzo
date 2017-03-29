<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Logger;

use Ouzo\Config;
use Ouzo\Utilities\Arrays;

abstract class AbstractLogger implements LoggerInterface
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

    protected function log($writeToLogFunction, $level, $levelName, $message, $params)
    {
        $minimalLevel = $this->_minimalLevels ? Arrays::getValue($this->_minimalLevels, $this->_name, LOG_DEBUG) : LOG_DEBUG;
        if ($level <= $minimalLevel) {
            $message = $this->_messageFormatter->format($this->_name, $levelName, $message);
            if (!empty($params)) {
                $message = call_user_func_array('sprintf', array_merge([$message], $params));
            }
            $writeToLogFunction($message);
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
