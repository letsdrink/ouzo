<?php
namespace Ouzo;

use Ouzo\Utilities\Path;

class ViewPathResolver
{
    public static function resolveViewPath($name, $responseType)
    {
        return Path::join(ROOT_PATH, 'application', 'view', $name . self::getViewPostfix($responseType));
    }

    private static function getViewPostfix($responseType)
    {
        if (Uri::isAjax()) {
            return '.ajax.phtml';
        }

        switch ($responseType) {
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