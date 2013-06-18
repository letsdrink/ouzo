<?php
namespace Thulium;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogHandler;

class Logger
{
    public static function getPanelLogger()
    {
        return self::_logger('panel');
    }

    public static function getSqlLogger()
    {
        return self::_logger('sql');
    }

    private static function _logger($name)
    {
        $log = new \Monolog\Logger($name);
        $log->pushHandler(self::_streamHandler());
        return $log;
    }

    private static function _streamHandler()
    {
        $logLevel = self::_isDebug() ? \Monolog\Logger::DEBUG : \Monolog\Logger::INFO;
        $stream = new SyslogHandler('PANEL', LOG_LOCAL3, $logLevel, true, LOG_ODELAY | LOG_PID);
        $stream->setFormatter(new LineFormatter('%channel%.%level_name%: [ID:' . FrontController::$requestId . '] [UserID:' . FrontController::$userId . '] %message% %context%'));
        return $stream;
    }

    private static function _isDebug()
    {
        return Config::load()->getConfig('debug');
    }
}