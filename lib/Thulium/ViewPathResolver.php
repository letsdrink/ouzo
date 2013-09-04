<?php

namespace Thulium;

use Thulium\Utilities\Arrays;

class ViewPathResolver
{

    public static function resolveViewPath($name)
    {
        return ROOT_PATH . 'application' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $name . self::getViewPostfix();
    }

    private static function getViewPostfix()
    {
        $contentType = Arrays::first(explode(';', Arrays::getValue($_SERVER, "CONTENT_TYPE")));
        if ($contentType == 'text/xml') {
            return '.xml.phtml';
        } else {
            return '.phtml';
        }
    }

}