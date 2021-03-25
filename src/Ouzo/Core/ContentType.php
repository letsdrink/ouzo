<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Utilities\Arrays;

class ContentType
{
    private static ?string $contentType = null;

    public static function set(?string $contentType): void
    {
        self::$contentType = $contentType;
    }

    private static function getFromServer(): string
    {
        return Arrays::first(explode(';', Arrays::getValue($_SERVER, 'CONTENT_TYPE')));
    }

    public static function value(): ?string
    {
        self::$contentType = self::$contentType ?: self::getFromServer();
        return self::$contentType;
    }
}
