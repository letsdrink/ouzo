<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class ContentType
{
    private static $contentType;

    public static function set($contentType)
    {
        self::$contentType = $contentType;
    }

    private static function getFromServer()
    {
        return Arrays::first(explode(';', Arrays::getValue($_SERVER, 'CONTENT_TYPE')));
    }

    public static function value()
    {
        self::$contentType = self::$contentType ?: self::getFromServer();
        return self::$contentType;
    }
}
