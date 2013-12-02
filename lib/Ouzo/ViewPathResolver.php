<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class ViewPathResolver
{
    public static function resolveViewPath($name)
    {
        return ROOT_PATH . 'application' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $name . self::getViewPostfix();
    }

    private static function getViewPostfix()
    {
        if (Uri::isAjax()) {
            return '.ajax.phtml';
        }
        $contentType = Arrays::first(explode(';', Arrays::getValue($_SERVER, 'CONTENT_TYPE')));
        return $contentType == 'text/xml' ? '.xml.phtml' : '.phtml';
    }
}