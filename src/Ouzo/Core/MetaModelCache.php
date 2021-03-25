<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

class MetaModelCache
{
    /** @var Model[] */
    private static array $modelMetaInstances = [];

    public static function getMetaInstance(string $modelClass): Model
    {
        if (!isset(self::$modelMetaInstances[$modelClass])) {
            self::$modelMetaInstances[$modelClass] = $modelClass::newInstance([]);
        }
        return self::$modelMetaInstances[$modelClass];
    }
}
