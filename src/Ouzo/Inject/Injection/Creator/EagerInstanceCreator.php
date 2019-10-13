<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Creator;


use ReflectionClass;

class EagerInstanceCreator implements InstanceCreator
{
    public function create($className, $arguments)
    {
        if ($arguments) {
            $class = new ReflectionClass($className);
            return $class->newInstanceArgs($arguments);
        }
        return new $className;
    }
}