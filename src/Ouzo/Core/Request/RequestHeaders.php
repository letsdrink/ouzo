<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Ouzo\Http\AcceptHeaderParser;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

class RequestHeaders
{
    private static $headers;

    public static function accept()
    {
        $accept = Arrays::getValue($_SERVER, 'HTTP_ACCEPT');
        return AcceptHeaderParser::parse($accept);
    }

    public static function ip()
    {
        $ip = Arrays::getValue($_SERVER, 'HTTP_CLIENT_IP');
        if (!$ip) {
            $ip = Arrays::getValue($_SERVER, 'HTTP_X_FORWARDED_FOR');
        }
        if (!$ip) {
            $ip = Arrays::getValue($_SERVER, 'REMOTE_ADDR');
        }
        return $ip;
    }

    public static function all()
    {
        //this implementation is for PHP where function getallheaders() doesn't exists in CLI
        if (!self::$headers) {
            $headers = Arrays::filterByKeys($_SERVER, Functions::startsWith('HTTP_'));
            self::$headers = Arrays::mapKeys($headers, function ($key) {
                return str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
            });
        }
        return self::$headers;
    }

    public static function clearCache() {
        self::$headers = null;
    }
}
