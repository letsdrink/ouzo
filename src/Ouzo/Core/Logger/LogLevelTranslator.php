<?php
namespace Ouzo\Logger;

use Psr\Log\LogLevel;

class LogLevelTranslator
{
    private static $levelMap = [
        LogLevel::EMERGENCY => LOG_EMERG,
        LogLevel::ALERT => LOG_ALERT,
        LogLevel::CRITICAL => LOG_CRIT,
        LogLevel::ERROR => LOG_ERR,
        LogLevel::WARNING => LOG_WARNING,
        LogLevel::NOTICE => LOG_NOTICE,
        LogLevel::INFO => LOG_INFO,
        LogLevel::DEBUG => LOG_DEBUG,
    ];

    public static function toSyslogLevel($psrLevel)
    {
        return self::$levelMap[$psrLevel];
    }
}
