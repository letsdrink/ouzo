<?php

namespace Ouzo\Logger;

use Ouzo\Config;
use Ouzo\Utilities\Arrays;

class StdOutputLogger implements LoggerInterface
{
    const STD_ERR = 'php://stderr';
    const STD_OUT = 'php://stdout';

    private $_name;
    /**
     * @var MessageFormatter
     */
    private $_messageFormatter;

    public function __construct($name)
    {
        $this->_name = $name;
        $messageFormatterClass = 'Ouzo\Logger\DefaultMessageFormatter';
        $logger = Config::getValue('logger');
        if ($logger) {
            $messageFormatterClass = Arrays::getValue($logger, 'formatter', $messageFormatterClass);
        }
        $this->_messageFormatter = new $messageFormatterClass();
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    private function log($stdOut, $levelName, $message, $params)
    {
        $message = $this->_messageFormatter->format($this->_name, $levelName, $message);
        if (!empty($params)) {
            $message = call_user_func_array('sprintf', array_merge(array($message), $params));
        }
        $fileHandle = fopen($stdOut, 'a');
        fwrite($fileHandle, $message);
        fclose($fileHandle);
    }

    public function error($message, $params = null)
    {
        $this->log(self::STD_ERR, 'Error', $message, $params);
    }

    public function info($message, $params = null)
    {
        $this->log(self::STD_OUT, 'Info', $message, $params);
    }

    public function debug($message, $params = null)
    {
        if ($this->_isDebug()) {
            $this->log(self::STD_OUT, 'Debug', $message, $params);
        }
    }

    private function _isDebug()
    {
        return Config::getValue('debug');
    }

    public function warning($message, $params = null)
    {
        $this->log(self::STD_OUT, 'Warning', $message, $params);
    }

    public function fatal($message, $params = null)
    {
        $this->log(self::STD_ERR, 'Fatal', $message, $params);
    }
}