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
        $contentType = Arrays::first(explode(';', Arrays::getValue($_SERVER, 'CONTENT_TYPE')));
        return $contentType == 'text/xml' ? '.xml.phtml' : '.phtml';
    }
}