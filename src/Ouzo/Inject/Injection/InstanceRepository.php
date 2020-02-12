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
    /** @var object[] */
    private $factoryInstances = [];
    /** @var Bindings */
    private $bindings;

    public function __construct(Bindings $bindings)
    {
        $this->bindings = $bindings;
    }

    public function getInstance(InstanceFactory $factory, Binder $binder): object
    {
        $instance = $binder->getInstance();
        if ($instance) {
            return $instance;
        }

        $className = $binder->getBoundClassName() ?: $binder->getClassName();

        $factoryClassName = $binder->getFactoryClassName();
        if ($factoryClassName) {
            return $this->createInstanceThroughFactory($factory, $className, $binder);
        }

        $scope = $binder->getScope();
        if ($scope == Scope::SINGLETON) {
            return $this->singletonInstance($factory, $className, $binder->isEager());
        }
        if ($scope == Scope::PROTOTYPE) {
            return $factory->createInstance($this, $className);
        }
        throw new BadMethodCallException("Unknown scope: $scope");
    }

    public function singletonInstance(InstanceFactory $factory, string $className, bool $eager): object
    {
        if (isset($this->instances[$className])) {
            return $this->instances[$className];
        }
        $instance = $factory->createInstance($this, $className, $eager);
        $this->instances[$className] = $instance;
        return $instance;
    }

    private function createInstanceThroughFactory(InstanceFactory $factory, string $className, Binder $binder): object
    {
        $factoryClassName = $binder->getFactoryClassName();

        if (!in_array(Factory::class, class_implements($factoryClassName))) {
            throw new InjectorException("Factory class $factoryClassName does not implemented \Ouzo\Injection\Factory interface.");
        }

        if ($binder->getScope() == Scope::SINGLETON) {
            if (isset($this->factoryInstances[$factoryClassName])) {
                return $this->factoryInstances[$factoryClassName];
            }
        }

        return $this->createInstanceThroughFactoryAsPrototype($factory, $factoryClassName, $className, $binder->isEager());
    }

    private function createInstanceThroughFactoryAsPrototype(InstanceFactory $factory, string $factoryClassName, string $className, bool $eager = true): object
    {
        $factoryBinder = $this->bindings->getBinder($factoryClassName);
        /** @var Factory $factoryObject */
        $factoryObject = $this->getInstance($factory, $factoryBinder);
        $object = $factory->createInstanceThroughFactory($this, $className, $factoryObject, $eager);
        $this->factoryInstances[$factoryClassName] = $object;

        return $object;
    }
}
