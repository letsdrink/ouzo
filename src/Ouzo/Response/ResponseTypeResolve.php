<?php

namespace Ouzo\Response;

use Ouzo\ContentType;
use Ouzo\Request\RequestHeaders;
use Ouzo\Utilities\Arrays;

class ResponseTypeResolve
{
    public static function resolve()
    {
        $accept = array_keys(RequestHeaders::accept()) ?: array('*/*');
        $supported = array(
            'application/json' => 'application/json',
            'application/xml' => 'application/xml',
            'application/*' => 'application/json',
            'text/html' => 'text/html',
            'text/*' => 'text/html'
        );
        $intersection = array_intersect($accept, array_keys($supported));
        if ($intersection) {
            return $supported[Arrays::first($intersection)];
        }
        return Arrays::getValue($supported, ContentType::value(), 'text/html');
    }
}