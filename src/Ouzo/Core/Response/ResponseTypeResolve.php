<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Response;

use Ouzo\ContentType;
use Ouzo\Request\RequestHeaders;
use Ouzo\Utilities\Arrays;

class ResponseTypeResolve
{
    const APPLICATION_ALL = 'application/*';
    const APPLICATION_JSON = 'application/json';
    const APPLICATION_XML = 'application/xml';
    const TEXT_ALL = 'text/*';
    const TEXT_HTML = 'text/html';
    const ALL = '*/*';

    public static function resolve()
    {
        $accept = array_keys(RequestHeaders::accept()) ?: [self::ALL];
        $supported = [
            self::APPLICATION_JSON => self::APPLICATION_JSON,
            self::APPLICATION_XML => self::APPLICATION_XML,
            self::APPLICATION_ALL => self::APPLICATION_JSON,
            self::TEXT_HTML => self::TEXT_HTML,
            self::TEXT_ALL => self::TEXT_HTML
        ];
        $intersection = array_intersect($accept, array_keys($supported));
        if ($intersection) {
            return $supported[Arrays::first($intersection)];
        }
        return Arrays::getValue($supported, ContentType::value(), self::TEXT_HTML);
    }
}
