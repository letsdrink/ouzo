<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
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
        $contentType = Arrays::getValue($_SERVER, 'CONTENT_TYPE', '');
        return Arrays::first(explode(';', $contentType));
    }

    public static function value(): ?string
    {
        self::$contentType = self::$contentType ?: self::getFromServer();
        return self::$contentType;
    }
}
