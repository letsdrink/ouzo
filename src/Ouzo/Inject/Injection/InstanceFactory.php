<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

use Ouzo\Injection\Annotation\AttributeInjectorRegistry;
use Ouzo\Injection\Creator\InstanceCreator;
use ReflectionClass;
use ReflectionProperty;

class InstanceFactory
{
    public function __construct(
        private InstanceCreator $eagerInstanceCreator,
        private InstanceCreator $lazyInstanceCreator,
        private AttributeInjectorRegistry $attributeInjectorRegistry
    )
    {
    }

    public function createInstance(InstanceRepository $repository, string $className, bool $eager = true): object
    {
        $instance = $this->constructInstance($repository, $className, $eager);
        if ($eager) {
            $this->injectDependencies($repository, $instance);
        }

        return $instance;
    }

    public function createInstanceThroughFactory(InstanceRepository $repository, string $className, Factory $factory, bool $eager = true): object
    {
        if ($eager || $this->lazyInstanceCreator === $this->eagerInstanceCreator) {
            return $this->eagerInstanceCreator->createThroughFactory($className, null, $repository, $this, $factory);
        }

        return $this->lazyInstanceCreator->createThroughFactory($className, null, $repository, $this, $factory);
    }

    private function injectDependencies(InstanceRepository $repository, object $instance, ReflectionClass $class = null): void
    {
        $propertyFilter = ReflectionProperty::IS_PRIVATE;
        if (is_null($class)) {
            $class = new ReflectionClass($instance);
            $propertyFilter = ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED;
        }
        $properties = $class->getProperties($propertyFilter);

        $attributeInjectors = $this->attributeInjectorRegistry->getAttributeInjectors();
        foreach ($attributeInjectors as $attributeInjector) {
            $attributeInjector->injectForProperties($instance, $properties, $this);
        }

        $parentClass = $class->getParentClass();
        if ($parentClass) {
            $this->injectDependencies($repository, $instance, $parentClass);
        }
    }

    private function constructInstance(InstanceRepository $repository, string $className, bool $eager = true): object
    {
        if ($eager || $this->lazyInstanceCreator === $this->eagerInstanceCreator) {
            $arguments = $this->getConstructorArguments($className);
            return $this->eagerInstanceCreator->create($className, $arguments, $repository, $this);
        }
        return $this->lazyInstanceCreator->create($className, null, $repository, $this);
    }

    private function getConstructorArguments(string $className): array
    {
        $instance = new ReflectionClass($className);
        $constructor = $instance->getConstructor();

        if (is_null($constructor)) {
            return [];
        }

        $arguments = [];
        $attributeInjectors = $this->attributeInjectorRegistry->getAttributeInjectors();
        foreach ($attributeInjectors as $attributeInjector) {
            $arguments = array_merge($arguments, $attributeInjector->injectForConstructorParameter($constructor, $this));
        }
        return $arguments;
    }
}
