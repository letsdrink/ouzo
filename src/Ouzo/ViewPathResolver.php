<?php
namespace Ouzo;

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

        switch (ContentType::getFromServer()) {
            case 'text/xml':
                return '.xml.phtml';
            case 'application/json':
            case 'text/json':
                return '.json.phtml';
            default:
                return '.phtml';
        }
    }
}