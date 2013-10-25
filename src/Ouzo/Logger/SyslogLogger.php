<?php
namespace Ouzo\Logger;

use Ouzo\Config;
use Ouzo\FrontController;

class SyslogLogger implements LoggerInterface
{
    private $_name;
    private $_logger;

    function __construct($name)
    {
        $this->_name = $name;

        $logger = Config::getValue('logger');
        if ($logger) {
            openlog($logger['ident'], $logger['option'], $logger['facility']);
        }

        $this->_logger = $logger;
    }

    function __destruct()
    {
        if ($this->_logger) {
            closelog();
        }
    }

    private function log($level, $levelName, $message, $params)
    {
        $message = sprintf("%s %s: [ID: %s] [UserID: %s] %s", $this->_name, $levelName, FrontController::$requestId, FrontController::$userId, $message);
        if (!empty($params)) {
            $message = call_user_func_array('sprintf', array_merge(array($message), $params));
        }
        syslog($level, $message);
    }

    public function error($message, $params = null)
    {
        $this->log(LOG_ERR, 'Error', $message, $params);
    }

    public function info($message, $params = null)
    {
        $this->log(LOG_INFO, 'Info', $message, $params);
    }

    public function debug($message, $params = null)
    {
        if ($this->_isDebug()) {
            $this->log(LOG_DEBUG, 'Debug', $message, $params);
        }
    }

    public function warning($message, $params = null)
    {
        $this->log(LOG_WARNING, 'Warning', $message, $params);
    }

    public function fatal($message, $params = null)
    {
        $this->log(LOG_CRIT, 'Fatal', $message, $params);
    }

    private function _isDebug()
    {
        return Config::getValue('debug');
    }

    function setName($name)
    {
        $this->_name = $name;
    }
}