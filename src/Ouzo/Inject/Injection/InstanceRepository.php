<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

use BadMethodCallException;

class InstanceRepository
{
    /** @var object[] */
    private $instances = [];

    /** @var Bindings */
    private $bindings;

    /**
     * @param Bindings $bindings
     */
    public function __construct(Bindings $bindings)
    {
        $this->bindings = $bindings;
    }

    /**
     * @param InstanceFactory $factory
     * @param Binder $binder
     * @return object
     * @throws BadMethodCallException
     * @throws InjectorException
     */
    public function getInstance(InstanceFactory $factory, Binder $binder)
    {
        $instance = $binder->getInstance();
        if ($instance) {
            return $instance;
        }

        $factoryClassName = $binder->getFactoryClassName();
        if ($factoryClassName) {
            return $this->createInstanceThroughFactory($factory, $factoryClassName);
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

    /**
     * @param InstanceFactory $factory
     * @param $className
     * @return object
     */
    public function singletonInstance(InstanceFactory $factory, $className)
    {
        if (isset($this->instances[$className])) {
            return $this->instances[$className];
        }
        $instance = $factory->createInstance($this, $className);
        $this->instances[$className] = $instance;
        return $instance;
    }

    /**
     * @param InstanceFactory $factory
     * @param $factoryClassName
     * @return mixed
     * @throws InjectorException
     */
    private function createInstanceThroughFactory(InstanceFactory $factory, $factoryClassName)
    {
        if (!in_array(Factory::class, class_implements($factoryClassName))) {
            throw new InjectorException("Factory class $factoryClassName does not implemented \Ouzo\Injection\Factory interface.");
        }
        $factoryBinder = $this->bindings->getBinder($factoryClassName);
        $factoryObject = $this->getInstance($factory, $factoryBinder);
        return $factoryObject->create();
    }
}
