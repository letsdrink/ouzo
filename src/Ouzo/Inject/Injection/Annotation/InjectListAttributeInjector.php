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
use ReflectionProperty;

class InjectListAttributeInjector implements AttributeInjector
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
            $injectList = $reflectionProperty->getAttributes(InjectList::class);

            if (!empty($injectList)) {
                if ($reflectionProperty->getType()?->getName() !== 'array') {
                    $class = $instance::class;
                    throw new InjectorException("Cannot #[InjectList] dependency - wrong type. " .
                        "Use array property \${$reflectionProperty->getName()} in class {$class}.");
                }
                $className = $injectList[0]->newInstance()->getName();

                $binder = $this->bindings->getBinder($className);
                $dependencyInstance = $this->instanceRepository->getInstance($instanceFactory, $binder);

                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($instance, [$dependencyInstance]);
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
                $injectList = $parameter->getAttributes(InjectList::class);

                if (!empty($injectList)) {
                    if ($parameter->getType()?->getName() !== 'array') {
                        throw new InjectorException("Cannot #[InjectList] dependency - wrong type. " .
                            "Use array property \${$parameter->getName()}.");
                    }
                    $className = $injectList[0]->newInstance()->getName();

                    $binder = $this->bindings->getBinder($className);
                    $dependencyInstance = $this->instanceRepository->getInstance($instanceFactory, $binder);

                    $constructorParameters[$parameter->getName()] = [$dependencyInstance];
                }
            }
        }

        return $constructorParameters;
    }
}
