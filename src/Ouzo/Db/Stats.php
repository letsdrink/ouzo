<?php
namespace Ouzo\Db;

use Ouzo\Config;
use Ouzo\Uri;
use Ouzo\Utilities\Arrays;

class Stats
{
    public static function queries()
    {
        self::initializeIfUnset();
        return $_SESSION['stats_queries'];
    }

    public static function reset()
    {
        self::initializeIfUnset();
        unset($_SESSION['stats_queries']);
    }

    public static function initializeIfUnset()
    {
        if (!isset($_SESSION['stats_queries'])) {
            $_SESSION['stats_queries'] = array();
        }
    }

    public static function trace($query, $params, $function)
    {
        if (Config::getValue('debug')) {
            self::initializeIfUnset();

            $startTime = microtime(true);
            $result = $function();
            $time = number_format(microtime(true) - $startTime, 4, '.', '');

            $uri = new Uri();
            $_SESSION['stats_queries'][$uri->getPathWithoutPrefix()][] = array('query' => $query, 'params' => $params, 'time' => $time, 'trace' => self::getBacktraceString());

            return $result;
        }
        return $function();
    }

    public static function getTotalTime()
    {
        return array_reduce(self::queries(), function ($sum, $value) {
            $value = Arrays::flatten($value);
            return $sum + $value['time'];
        });
    }

    public static function getNumberOfQueries()
    {
        return sizeof(self::queries());
    }

    static function getBacktraceString()
    {
        $trace = debug_backtrace();
        $trace = array_slice($trace, 2);

        $stack = '';
        foreach ($trace as $index => $frame) {
            $stack .= self::formatStackFrame($index + 1, $frame);
        }
        return $stack;
    }

    static function formatStackFrame($index, array $frame)
    {
        $stack = '';
        if (isset($frame['file']) && isset($frame['line']) && isset($frame['function'])) {
            ;
            $stack .= "#$index {$frame['file']} ({$frame['line']}): ";
            if (isset($frame['class'])) {
                $stack .= $frame['class'] . "->";
            }
            $stack .= $frame['function'] . "()" . PHP_EOL;
        }
        return $stack;
    }
}