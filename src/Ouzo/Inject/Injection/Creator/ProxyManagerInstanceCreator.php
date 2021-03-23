<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Creator;

use Ouzo\Injection\Factory;
use Ouzo\Injection\InstanceFactory;
use Ouzo\Injection\InstanceRepository;
use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;

class ProxyManagerInstanceCreator implements InstanceCreator
{
    private LazyLoadingValueHolderFactory $factory;

    public function __construct(Configuration $configuration)
    {
        $this->factory = new LazyLoadingValueHolderFactory($configuration);
    }

    public function create(string $className, ?array $arguments, InstanceRepository $repository, InstanceFactory $instanceFactory): object
    {
        return $this->factory->createProxy(
            $className,
            function (&$wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, &$initializer) use ($className, $repository, $instanceFactory) {
                $wrappedObject = $instanceFactory->createInstance($repository, $className);
                $initializer = null;
                return true;
            }
        );
    }

    public function createThroughFactory(string $className, ?array $arguments, InstanceRepository $repository, InstanceFactory $instanceFactory, Factory $factory): object
    {
        return $this->factory->createProxy(
            $className,
            function (&$wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, &$initializer) use ($className, $repository, $instanceFactory, $factory) {
                $wrappedObject = $instanceFactory->createInstanceThroughFactory($repository, $className, $factory);
                $initializer = null;
                return true;
            }
        );
    }
}
