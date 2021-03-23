<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Closure;
use Ouzo\Config;

class Stats
{
    public static array $queries = [];

    public static function trace(string $query, mixed $params, Closure $function): mixed
    {
        $traceEnabled = Config::getValue('debug') && Config::getValue('stats_disabled') !== true;
        if ($traceEnabled) {
            return self::traceNoCheck($query, $params, $function);
        }
        return $function();
    }

    public static function getTotalTime(): int
    {
        return array_reduce(self::$queries, fn($sum, $value) => $sum + $value['time']);
    }

    public static function reset(): void
    {
        Stats::$queries = [];
    }

    public static function traceNoCheck(string $query, mixed $params, Closure $function): mixed
    {
        $startTime = microtime(true);
        $result = $function();
        $time = number_format(microtime(true) - $startTime, 4, '.', '');

        self::$queries[] = [
            'query' => $query,
            'params' => $params,
            'time' => $time,
            'trace' => self::getBacktraceString()
        ];

        return $result;
    }

    private static function getBacktraceString(): string
    {
        $trace = debug_backtrace();
        $trace = array_slice($trace, 2);

        $stack = '';
        foreach ($trace as $index => $frame) {
            $stack .= self::formatStackFrame($index + 1, $frame);
        }

        return $stack;
    }

    private static function formatStackFrame(string $index, array $frame): string
    {
        $stack = '';
        if (isset($frame['file']) && isset($frame['line']) && isset($frame['function'])) {
            $stack .= "#$index {$frame['file']} ({$frame['line']}): ";
            if (isset($frame['class'])) {
                $stack .= "{$frame['class']}->";
            }
            $stack = "{$stack}{$frame['function']}()" . PHP_EOL;
        }

        return $stack;
    }
}
