<?php
namespace Ouzo\Request;

use Ouzo\Http\AcceptHeaderParser;

class RequestHeaders
{
    public static function accept()
    {
        return AcceptHeaderParser::parse($_SERVER['HTTP_ACCEPT']);
    }
}