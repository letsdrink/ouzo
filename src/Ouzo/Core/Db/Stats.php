<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Ouzo\Config;

class Stats
{
    public static $queries = [];

    /**
     * @param string $query
     * @param array $params
     * @param callable $function
     * @return mixed
     */
    public static function trace($query, $params, $function)
    {
        $traceEnabled = Config::getValue('debug') && Config::getValue('stats_disabled') !== true;
        if ($traceEnabled) {
            return self::traceNoCheck($query, $params, $function);
        }
        return $function();
    }

    /**
     * @return int
     */
    public static function getTotalTime()
    {
        return array_reduce(self::$queries, function ($sum, $value) {
            return $sum + $value['time'];
        });
    }

    public static function reset()
    {
        Stats::$queries = [];
    }

    /**
     * @param string $query
     * @param array $params
     * @param callable $function
     * @return mixed
     */
    public static function traceNoCheck($query, $params, $function)
    {
        $startTime = microtime(true);
        $result = $function();
        $time = number_format(microtime(true) - $startTime, 4, '.', '');

        self::$queries[] = ['query' => $query, 'params' => $params, 'time' => $time, 'trace' => self::getBacktraceString()];

        return $result;
    }

    /**
     * @return string
     */
    private static function getBacktraceString()
    {
        $trace = debug_backtrace();
        $trace = array_slice($trace, 2);

        $stack = '';
        foreach ($trace as $index => $frame) {
            $stack .= self::formatStackFrame($index + 1, $frame);
        }

        return $stack;
    }

    /**
     * @param string $index
     * @param array $frame
     * @return string
     */
    private static function formatStackFrame($index, array $frame)
    {
        $stack = '';
        if (isset($frame['file']) && isset($frame['line']) && isset($frame['function'])) {
            $stack .= "#$index {$frame['file']} ({$frame['line']}): ";
            if (isset($frame['class'])) {
                $stack .= $frame['class'] . "->";
            }
            $stack .= $frame['function'] . "()" . PHP_EOL;
        }

        return $stack;
    }
}
