<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Creator;


use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;

class ProxyManagerInstanceCreator implements InstanceCreator
{
    /** @var EagerInstanceCreator */
    private $eagerInstanceFactory;
    /** @var LazyLoadingValueHolderFactory */
    private $factory;

    public function __construct(Configuration $configuration)
    {
        $this->eagerInstanceFactory = new EagerInstanceCreator();
        $this->factory = new LazyLoadingValueHolderFactory($configuration);
    }

    public function create($className, $arguments)
    {
        return $this->factory->createProxy(
            $className,
            function (& $wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, & $initializer) use ($className, $arguments) {
                $wrappedObject = $this->eagerInstanceFactory->create($className, $arguments);
                $initializer = null;
                return true;
            }
        );
    }
}