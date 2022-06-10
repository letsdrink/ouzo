<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

use BadMethodCallException;
use Ouzo\Utilities\Arrays;

class InstanceRepository
{
    /** @var object[] */
    private array $instances = [];
    /** @var object[] */
    private array $factoryInstances = [];

    public function __construct(private Bindings $bindings)
    {
    }

    public function getInstance(InstanceFactory $factory, Binder $binder): object
    {
        $className = Arrays::firstOrNull($binder->getBoundClassNames()) ?: $binder->getClassName();

        return $this->getSingleInstance($className, $binder, $factory);
    }

    /**
     * This is used for multi bindings and #[InjectList].
     */
    public function getListOfInstances(InstanceFactory $factory, Binder $binder): array
    {
        $classNames = $binder->getBoundClassNames();
        if (sizeof($classNames) < 1) {
            throw new InjectorException("Invalid configuration for `{$binder->getClassName()}` (name: `{$binder->getName()}`). There are no bound class names.");
        }

        return Arrays::map($classNames, fn($className) => $this->getSingleInstance($className, $binder, $factory));
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

    private function getSingleInstance(string $className, Binder $binder, InstanceFactory $factory): object
    {
        $instance = $binder->getInstance();
        if (!is_null($instance)) {
            return $instance;
        }

        $factoryClassName = $binder->getFactoryClassName();
        if (!is_null($factoryClassName)) {
            return $this->createInstanceThroughFactory($factory, $className, $binder);
        }

        $scope = $binder->getScope();
        if ($scope === Scope::SINGLETON) {
            return $this->singletonInstance($factory, $className, $binder->isEager());
        }
        if ($scope === Scope::PROTOTYPE) {
            return $factory->createInstance($this, $className);
        }

        throw new BadMethodCallException("Unknown scope: {$scope}");
    }

    private function createInstanceThroughFactory(InstanceFactory $factory, string $className, Binder $binder): object
    {
        $factoryClassName = $binder->getFactoryClassName();

        if (!in_array(Factory::class, class_implements($factoryClassName))) {
            throw new InjectorException("Factory class {$factoryClassName} does not implemented \Ouzo\Injection\Factory interface.");
        }

        if ($binder->getScope() === Scope::SINGLETON) {
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
