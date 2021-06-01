<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Annotation;

use Ouzo\Injection\Bindings;
use Ouzo\Injection\InjectorException;
use Ouzo\Injection\InstanceFactory;
use Ouzo\Injection\InstanceRepository;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;

class InjectAttributeInjector implements AttributeInjector
{
    public function __construct(
        private Bindings $bindings,
        private InstanceRepository $instanceRepository
    )
    {
    }

    /** @param ReflectionProperty[] $reflectionProperties */
    public function injectForProperties(object $instance, array $reflectionProperties, InstanceFactory $instanceFactory): void
    {
        foreach ($reflectionProperties as $reflectionProperty) {
            $inject = $reflectionProperty->getAttributes(Inject::class);
            $named = $reflectionProperty->getAttributes(Named::class);
            $named = !empty($named) ? $named[0] : null;

            if (!empty($inject)) {
                $name = $named?->newInstance()->getName() ?: '';
                if (!$reflectionProperty->hasType()) {
                    $class = $instance::class;
                    throw new InjectorException("Cannot #[Inject] dependency - missing type. " .
                        "Use typed property \${$reflectionProperty->getName()} in class {$class}.");
                }
                $className = $reflectionProperty->getType()->getName();

                $binder = $this->bindings->getBinder($className, $name);
                $dependencyInstance = $this->instanceRepository->getInstance($instanceFactory, $binder);

                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($instance, $dependencyInstance);
            }
        }
    }

    public function injectForConstructorParameter(ReflectionMethod $constructor, InstanceFactory $instanceFactory): array
    {
        $constructorParameters = [];

        $attributes = $constructor->getAttributes(Inject::class);
        if (!empty($attributes)) {
            $parameters = $constructor->getParameters();
            foreach ($parameters as $parameter) {
                $named = $parameter->getAttributes(Named::class);
                $type = $parameter->getType();
                if (is_null($type) || !($type instanceof ReflectionNamedType)) {
                    throw new InjectorException("Cannot #[Inject] by constructor for class {}. " .
                        "All arguments should have types defined (but not union types!).");
                }

                if ($type->isBuiltin()) {
                    continue;
                }

                $parameterName = $parameter->getName();
                $name = !empty($named) ? $named[0]->newInstance()->getName() : '';

                $binder = $this->bindings->getBinder($type->getName(), $name);
                $dependencyInstance = $this->instanceRepository->getInstance($instanceFactory, $binder);

                $constructorParameters[$parameterName] = $dependencyInstance;
            }
        }

        return $constructorParameters;
    }
}
