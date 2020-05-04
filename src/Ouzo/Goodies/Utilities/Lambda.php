<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Utilities;

use Ouzo\Model;

class Lambda
{
    public static function id()
    {
        return function (Model $object) {
            return $object->getId();
        };
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return (new NonCallableExtractor())->$name(...$arguments);
    }
}
