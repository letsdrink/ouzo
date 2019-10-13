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
            return $this->createInstanceThroughFactory($factory, $binder);
        }

        $className = $binder->getBoundClassName() ?: $binder->getClassName();
        $scope = $binder->getScope();
        if ($scope == Scope::SINGLETON) {
            return $this->singletonInstance($factory, $className, $binder->isEager());
        }
        if ($scope == Scope::PROTOTYPE) {
            return $factory->createInstance($this, $className);
        }
        throw new BadMethodCallException("Unknown scope: $scope");
    }

    /**
     * @param InstanceFactory $factory
     * @param $className
     * @param bool $eager
     * @return object
     */
    public function singletonInstance(InstanceFactory $factory, $className, $eager)
    {
        if (isset($this->instances[$className])) {
            return $this->instances[$className];
        }
        $instance = $factory->createInstance($this, $className, $eager);
        $this->instances[$className] = $instance;
        return $instance;
    }

    /**
     * @param InstanceFactory $factory
     * @param Binder $binder
     * @return mixed
     * @throws InjectorException
     */
    private function createInstanceThroughFactory(InstanceFactory $factory, Binder $binder)
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

        return $this->createInstanceThroughFactoryAsPrototype($factory, $factoryClassName);
    }

    /**
     * @param InstanceFactory $factory
     * @param string $factoryClassName
     * @return mixed
     */
    private function createInstanceThroughFactoryAsPrototype(InstanceFactory $factory, $factoryClassName)
    {
        $factoryBinder = $this->bindings->getBinder($factoryClassName);
        /** @var Factory $factoryObject */
        $factoryObject = $this->getInstance($factory, $factoryBinder);
        $object = $factoryObject->create();
        $this->factoryInstances[$factoryClassName] = $object;

        return $object;
    }
}
