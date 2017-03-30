<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

class MetaModelCache
{
    /**
     * @var Model
     */
    private static $modelMetaInstances = [];

    /**
     * @param string $modelClass
     * @return Model
     */
    public static function getMetaInstance($modelClass)
    {
        if (!isset(self::$modelMetaInstances[$modelClass])) {
            self::$modelMetaInstances[$modelClass] = $modelClass::newInstance([]);
        }
        return self::$modelMetaInstances[$modelClass];
    }
}
