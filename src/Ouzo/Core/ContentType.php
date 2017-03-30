<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class ContentType
{
    /** @var string */
    private static $contentType;

    /**
     * @param string $contentType
     * @return void
     */
    public static function set($contentType)
    {
        self::$contentType = $contentType;
    }

    /**
     * @return string
     */
    private static function getFromServer()
    {
        return Arrays::first(explode(';', Arrays::getValue($_SERVER, 'CONTENT_TYPE')));
    }

    /**
     * @return string
     */
    public static function value()
    {
        self::$contentType = self::$contentType ?: self::getFromServer();
        return self::$contentType;
    }
}
