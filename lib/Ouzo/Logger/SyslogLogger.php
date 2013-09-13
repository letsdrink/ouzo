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

        $logger = Config::load()->getConfig('logger');
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

    private function log($level, $levelName, $message)
    {
        $message = sprintf("%s %s: [ID: %s] [UserID: %s] %s", $this->_name, $levelName, FrontController::$requestId, FrontController::$userId, $message);
        syslog($level, $message);
    }

    public function error($message)
    {
        $this->log(LOG_ERR, 'Error', $message);
    }

    public function info($message)
    {
        $this->log(LOG_INFO, 'Info', $message);
    }

    public function debug($message)
    {
        if ($this->_isDebug()) {
            $this->log(LOG_DEBUG, 'Debug', $message);
        }
    }

    public function warning($message)
    {
        $this->log(LOG_WARNING, 'Warning', $message);
    }

    public function fatal($message)
    {
        $this->log(LOG_CRIT, 'Fatal', $message);
    }

    private function _isDebug()
    {
        return Config::load()->getConfig('debug');
    }
}