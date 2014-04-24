<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;

class ViewPathResolver
{
    public static function resolveViewPath($name)
    {
        return Path::join(ROOT_PATH, 'application', 'view', $name . self::getViewPostfix());
    }

    private static function getViewPostfix()
    {
        if (Uri::isAjax()) {
            return '.ajax.phtml';
        }

        $contentType = Arrays::first(explode(';', Arrays::getValue($_SERVER, 'CONTENT_TYPE')));
        switch ($contentType) {
            case 'text/xml':
                return '.xml.phtml';
            case 'application/json':
                return '.json.phtml';
            default:
                return '.phtml';
        }
    }
}