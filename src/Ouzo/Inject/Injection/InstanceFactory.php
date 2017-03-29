<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

use Ouzo\Injection\Annotation\AnnotationMetadataProvider;
use Ouzo\Utilities\Arrays;
use ReflectionClass;

class InstanceFactory
{
    /**
     * @var Bindings
     */
    private $bindings;
    /**
     * @var AnnotationMetadataProvider
     */
    private $provider;

    public function __construct(Bindings $bindings, AnnotationMetadataProvider $provider)
    {
        $this->bindings = $bindings;
        $this->provider = $provider;
    }

    public function createInstance(InstanceRepository $repository, $className)
    {
        $instance = $this->constructInstance($repository, $className);
        $this->injectDependencies($repository, $instance);
        return $instance;
    }

    private function injectDependencies(InstanceRepository $repository, $instance)
    {
        $annotations = $this->provider->getMetadata($instance);
        $class = new ReflectionClass($instance);
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            $annotation = Arrays::getValue($annotations, $property->getName());
            if ($annotation) {
                $dependencyInstance = $this->getInstance($repository, $annotation);
                $property->setAccessible(true);
                $property->setValue($instance, $dependencyInstance);
            }
        }
    }

    private function constructInstance(InstanceRepository $repository, $className)
    {
        $arguments = $this->getConstructorArguments($repository, $className);
        if ($arguments) {
            $class = new ReflectionClass($className);
            return $class->newInstanceArgs($arguments);
        }
        return new $className;
    }

    private function getConstructorArguments(InstanceRepository $repository, $className)
    {
        $annotations = $this->provider->getConstructorMetadata($className);
        return Arrays::map($annotations, function ($annotation) use ($repository) {
            return $this->getInstance($repository, $annotation);
        });
    }

    private function getInstance(InstanceRepository $repository, $annotation)
    {
        $binder = $this->bindings->getBinder($annotation['className'], $annotation['name']);
        return $repository->getInstance($this, $binder);
    }
}
