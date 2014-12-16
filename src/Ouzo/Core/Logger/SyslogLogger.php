<?php
namespace Ouzo\Logger;

use Ouzo\Config;
use Ouzo\Utilities\Arrays;

class SyslogLogger implements LoggerInterface
{
    private $_name;
    private $_configuration;

    public function __construct($name, $configuration)
    {
        $this->_name = $name;
        $this->_configuration = $configuration;
    }

    public function __destruct()
    {
        closelog();
    }

    private function log($level, $levelName, $message, $params)
    {
        $messageFormatterClass = '\Ouzo\Logger\DefaultMessageFormatter';
        $logger = Config::getValue('logger', $this->_configuration);
        if ($logger) {
            openlog($logger['ident'], $logger['option'], $logger['facility']);
            $messageFormatterClass = Arrays::getValue($logger, 'formatter', $messageFormatterClass);
        }

        $messageFormatter = new $messageFormatterClass();
        $message = $messageFormatter->format($this->_name, $levelName, $message);
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

    public function setName($name)
    {
        $this->_name = $name;
    }
}
