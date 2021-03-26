<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;

class ViewPathResolver
{
    public static function resolveViewPath(string $name, string $responseType): string
    {
        return Path::join(ROOT_PATH, ApplicationPaths::getViewPath(), $name . self::getViewPostfix($responseType));
    }

    private static function getViewPostfix(string $responseType): string
    {
        $availableViewsMap = [
            'text/xml' => '.xml.phtml',
            'application/json' => '.json.phtml',
            'text/json' => '.json.phtml',
        ];

        $viewForType = Arrays::getValue($availableViewsMap, $responseType, false);
        if ($viewForType) {
            return $viewForType;
        }

        return Uri::isAjax() ? '.ajax.phtml' : '.phtml';
    }
}
