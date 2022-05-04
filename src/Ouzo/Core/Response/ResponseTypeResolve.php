<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Response;

use Ouzo\ContentType;
use Ouzo\Http\MediaType;
use Ouzo\Request\RequestHeaders;
use Ouzo\Utilities\Arrays;

class ResponseTypeResolve
{
    const APPLICATION_ALL = 'application/*';
    const TEXT_ALL = 'text/*';

    public static function resolve(): string
    {
        $accept = array_keys(RequestHeaders::accept()) ?: [MediaType::ALL];
        $supported = [
            MediaType::APPLICATION_JSON => MediaType::APPLICATION_JSON,
            MediaType::APPLICATION_XML => MediaType::APPLICATION_XML,
            self::APPLICATION_ALL => MediaType::APPLICATION_JSON,
            MediaType::TEXT_HTML => MediaType::TEXT_HTML,
            self::TEXT_ALL => MediaType::TEXT_HTML,
        ];
        $intersection = array_intersect($accept, array_keys($supported));
        if ($intersection) {
            return $supported[Arrays::first($intersection)];
        }
        return Arrays::getValue($supported, ContentType::value(), MediaType::TEXT_HTML);
    }
}
