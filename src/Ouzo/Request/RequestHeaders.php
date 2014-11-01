<?php
namespace Ouzo\Request;

use Ouzo\Http\AcceptHeaderParser;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class RequestHeaders
{
    public static function accept()
    {
        $accept = Arrays::getValue($_SERVER, 'HTTP_ACCEPT');
        return AcceptHeaderParser::parse($accept);
    }

    public static function all()
    {
        //this implementation is for PHP where function getallheaders() doesn't exists in CLI
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (Strings::startsWith($name, 'HTTP_')) {
                $headerName = Strings::removePrefix($name, 'HTTP_');
                $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $headerName))));
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}