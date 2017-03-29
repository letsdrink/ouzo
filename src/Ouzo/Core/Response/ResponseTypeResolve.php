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
    public static function resolve()
    {
        $accept = array_keys(RequestHeaders::accept()) ? : ['*/*'];
        $supported = [
            'application/json' => 'application/json',
            'application/xml' => 'application/xml',
            'application/*' => 'application/json',
            'text/html' => 'text/html',
            'text/*' => 'text/html'
        ];
        $intersection = array_intersect($accept, array_keys($supported));
        if ($intersection) {
            return $supported[Arrays::first($intersection)];
        }
        return Arrays::getValue($supported, ContentType::value(), 'text/html');
    }
}
