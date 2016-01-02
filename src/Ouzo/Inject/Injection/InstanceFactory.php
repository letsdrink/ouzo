<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;


use Ouzo\Utilities\Strings;
use ReflectionClass;

class InstanceFactory
{

    /**
     * @var InjectorConfig
     */
    private $config;

    function __construct(InjectorConfig $config)
    {
        $this->config = $config;
    }

    public function createInstance(InstanceRepository $repository, $className)
    {
        $instance = new $className();
        $this->injectDependencies($repository, $instance);
        return $instance;
    }

    private function injectDependencies(InstanceRepository $repository, $instance)
    {
        $class = new ReflectionClass($instance);
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            $doc = $property->getDocComment();
            if (Strings::contains($doc, '@Inject')) {
                if (preg_match("#@var ([\\\\A-Za-z0-9]*)#s", $doc, $matched)) {
                    $dependency = $matched[1];
                    $binder = $this->config->getBinder($dependency);
                    $dependencyInstance = $repository->getInstance($this, $binder);
                    $property->setAccessible(true);
                    $property->setValue($instance, $dependencyInstance);
                } else {
                    throw new InjectorException('Cannot @Inject dependency. @var is not defined for property $' . $property->getName() . ' in class ' . $class->getName() . '.');
                }
            }
        }
    }
}