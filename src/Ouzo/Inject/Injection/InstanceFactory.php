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
    /** @var Bindings */
    private $bindings;
    /** @var AnnotationMetadataProvider */
    private $provider;

    /**
     * @param Bindings $bindings
     * @param AnnotationMetadataProvider $provider
     */
    public function __construct(Bindings $bindings, AnnotationMetadataProvider $provider)
    {
        $this->bindings = $bindings;
        $this->provider = $provider;
    }

    /**
     * @param InstanceRepository $repository
     * @param string $className
     * @return object
     */
    public function createInstance(InstanceRepository $repository, $className)
    {
        $instance = $this->constructInstance($repository, $className);
        $this->injectDependencies($repository, $instance);
        return $instance;
    }

    /**
     * @param InstanceRepository $repository
     * @param string $instance
     * @param ReflectionClass $class
     * @return void
     */
    private function injectDependencies(InstanceRepository $repository, $instance, ReflectionClass $class = null)
    {
        $parent = true;
        if ($class == null) {
            $class = new ReflectionClass($instance);
            $parent = false;
        }
        $annotations = $this->provider->getMetadata($class, $parent);
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            $annotation = Arrays::getValue($annotations, $property->getName());
            if ($annotation) {
                $dependencyInstance = $this->getInstance($repository, $annotation);
                $property->setAccessible(true);
                $property->setValue($instance, $dependencyInstance);
            }
        }
        $parentClass = $class->getParentClass();
        if ($parentClass) {
            $this->injectDependencies($repository, $instance, $parentClass);
        }
    }

    /**
     * @param InstanceRepository $repository
     * @param string $className
     * @return object
     */
    private function constructInstance(InstanceRepository $repository, $className)
    {
        $arguments = $this->getConstructorArguments($repository, $className);
        if ($arguments) {
            $class = new ReflectionClass($className);
            return $class->newInstanceArgs($arguments);
        }
        return new $className;
    }

    /**
     * @param InstanceRepository $repository
     * @param string $className
     * @return array
     */
    private function getConstructorArguments(InstanceRepository $repository, $className)
    {
        $annotations = $this->provider->getConstructorMetadata($className);
        return Arrays::map($annotations, function ($annotation) use ($repository) {
            return $this->getInstance($repository, $annotation);
        });
    }

    /**
     * @param InstanceRepository $repository
     * @param array $annotation
     * @return mixed
     */
    private function getInstance(InstanceRepository $repository, $annotation)
    {
        $binder = $this->bindings->getBinder($annotation['className'], $annotation['name']);
        return $repository->getInstance($this, $binder);
    }
}
