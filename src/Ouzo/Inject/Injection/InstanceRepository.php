<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;

use BadMethodCallException;

class InstanceRepository
{
    private $instances = array();

    public function getInstance(InstanceFactory $factory, Binder $binder)
    {
        $instance = $binder->getInstance();
        if ($instance) {
            return $instance;
        }
        $className = $binder->getBoundClassName() ?: $binder->getClassName();
        $scope = $binder->getScope();
        if ($scope == Scope::SINGLETON) {
            return $this->singletonInstance($factory, $className);
        }
        if ($scope == Scope::PROTOTYPE) {
            return $factory->createInstance($this, $className);
        }
        throw new BadMethodCallException("Unknown scope: $scope");
    }

    public function singletonInstance(InstanceFactory $factory, $className)
    {
        if (isset($this->instances[$className])) {
            return $this->instances[$className];
        }
        $instance = $factory->createInstance($this, $className);
        $this->instances[$className] = $instance;
        return $instance;
    }
}
