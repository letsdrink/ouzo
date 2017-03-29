<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

class MetaModelCache
{
    private static $_modelMetaInstances = [];

    /**
     * @param $modelClass
     * @return Model
     */
    public static function getMetaInstance($modelClass)
    {
        if (!isset(self::$_modelMetaInstances[$modelClass])) {
            self::$_modelMetaInstances[$modelClass] = $modelClass::newInstance([]);
        }
        return self::$_modelMetaInstances[$modelClass];
    }
}
