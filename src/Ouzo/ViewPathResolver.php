<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;

class ViewPathResolver
{
    public static function resolveViewPath($name, $responseType)
    {
        return Path::join(ROOT_PATH, 'application', 'view', $name . self::getViewPostfix($responseType));
    }

    private static function getViewPostfix($responseType)
    {
        $availableViewsMap = array(
            'text/xml' => '.xml.phtml',
            'application/json' => '.json.phtml',
            'text/json' => '.json.phtml',
        );

        $viewForType = Arrays::getValue($availableViewsMap, $responseType, false);
        if ($viewForType) {
            return $viewForType;
        }

        if (Uri::isAjax()) {
            return '.ajax.phtml';
        } else {
            return '.phtml';
        }
    }
}
