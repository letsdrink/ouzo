<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Creator;


use Ouzo\Injection\InstanceFactory;
use Ouzo\Injection\InstanceRepository;
use ReflectionClass;

class EagerInstanceCreator implements InstanceCreator
{
    public function create(string $className, ?array $arguments, InstanceRepository $repository, InstanceFactory $instanceFactory)
    {
        if ($arguments) {
            $class = new ReflectionClass($className);
            return $class->newInstanceArgs($arguments);
        }
        return new $className;
    }
}