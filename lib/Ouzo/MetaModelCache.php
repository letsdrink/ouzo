<?php

namespace Ouzo;

class MetaModelCache
{
    private static $_modelMetaInstances = array();

    public static function getMetaInstance($modelClass)
    {
        if (!isset(self::$_modelMetaInstances[$modelClass])) {
            self::$_modelMetaInstances[$modelClass] = $modelClass::newInstance(array());
        }
        return self::$_modelMetaInstances[$modelClass];
    }
}