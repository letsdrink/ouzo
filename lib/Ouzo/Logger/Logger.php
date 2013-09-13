<?php
namespace Ouzo\Logger;

use Ouzo\Config;

class Logger
{
    public static function getLogger($name)
    {
        $logger = Config::load()->getConfig('logger');
        return new $logger($name);
    }
}

