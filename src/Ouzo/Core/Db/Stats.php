<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Config;
use Ouzo\FrontController;
use Ouzo\Session;
use Ouzo\Uri;

class Stats
{
    const NUMBER_OF_REQUESTS_TO_KEEP = 10;

    public static function queries()
    {
        return Session::get('stats_queries') ? : [];
    }

    public static function queriesForRequest($request)
    {
        return Session::get('stats_queries', $request, 'queries') ? : [];
    }

    public static function reset()
    {
        Session::remove('stats_queries');
    }

    public static function trace($query, $params, $function)
    {
        if (Config::getValue('debug')) {
            return self::traceNoCheck($query, $params, $function);
        }
        return $function();
    }

    public static function traceNoCheck($query, $params, $function)
    {
        $startTime = microtime(true);
        $result = $function();
        $time = number_format(microtime(true) - $startTime, 4, '.', '');

        $uri = new Uri();
        $requestDetails = $uri->getPathWithoutPrefix() . '#' . FrontController::$requestId;
        $value = ['query' => $query, 'params' => $params, 'time' => $time, 'trace' => self::getBacktraceString()];
        Session::push('stats_queries', $requestDetails, 'queries', $value);

        self::removeExcessiveRequests();

        return $result;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public static function getTotalTime()
    {
        $sum = 0;
        $queries = self::queries();
        array_walk($queries, function ($data, $request) use (&$sum) {
            $sum += Stats::getRequestTotalTime($request);
        });
        return $sum;
    }

    public static function getRequestTotalTime($request)
    {
        return array_reduce(self::queriesForRequest($request), function ($sum, $value) {
            return $sum + $value['time'];
        });
    }

    public static function getNumberOfRequests()
    {
        return sizeof(self::queries());
    }

    public static function getNumberOfQueries()
    {
        $sum = 0;
        $queries = self::queries();
        array_walk($queries, function ($data, $request) use (&$sum) {
            $sum += Stats::getRequestNumberOfQueries($request);
        });
        return $sum;
    }

    public static function getRequestNumberOfQueries($request)
    {
        return sizeof(self::queriesForRequest($request));
    }

    public static function getBacktraceString()
    {
        $trace = debug_backtrace();
        $trace = array_slice($trace, 2);

        $stack = '';
        foreach ($trace as $index => $frame) {
            $stack .= self::formatStackFrame($index + 1, $frame);
        }
        return $stack;
    }

    public static function formatStackFrame($index, array $frame)
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

    public static function traceHttpRequest($params)
    {
        $uri = new Uri();
        $requestDetails = $uri->getPathWithoutPrefix() . '#' . FrontController::$requestId;
        Session::push('stats_queries', $requestDetails, 'request_params', $params);
    }

    private static function removeExcessiveRequests()
    {
        $all = Session::get('stats_queries');
        if (sizeof($all) > self::NUMBER_OF_REQUESTS_TO_KEEP) {
            while (sizeof($all) > self::NUMBER_OF_REQUESTS_TO_KEEP) {
                array_shift($all);
            }
            Session::set('stats_queries', $all);
        }
    }
}
