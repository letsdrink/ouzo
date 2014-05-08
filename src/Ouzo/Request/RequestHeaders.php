<?php
namespace Ouzo\Request;

use Ouzo\Http\AcceptHeaderParser;
use Ouzo\Utilities\Arrays;

class RequestHeaders
{
    public static function accept()
    {
        $accept = Arrays::getValue($_SERVER, 'HTTP_ACCEPT');
        return AcceptHeaderParser::parse($accept);
    }
}