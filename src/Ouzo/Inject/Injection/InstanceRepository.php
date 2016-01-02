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
        $className = $binder->getClassName();
        $scope = $binder->getScope();
        if ($scope == Scope::SINGLETON) {
            if (isset($this->instances[$className])) {
                return $this->instances[$className];
            }
            $instance = $factory->createInstance($this, $className);
            $this->instances[$className] = $instance;
            return $instance;
        }
        if ($scope == Scope::PROTOTYPE) {
            return $factory->createInstance($this, $className);
        }
        throw new BadMethodCallException("Unknown scope: $scope");
    }
}