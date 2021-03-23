<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

use Doctrine\Common\Annotations\AnnotationReader;
use Ouzo\Injection\Annotation\InjectMetadataProvider;
use Ouzo\Injection\Annotation\AttributesInjectMetadataProvider;

class Injector
{
    /** @param InjectorConfig */
    private $injectorConfig;
    /** @var Bindings */
    private $bindings;
    /** @var InstanceRepository */
    private $repository;
    /** @var InstanceFactory */
    private $factory;

    /**
     * @param InjectorConfig|null $config
     * @param InjectMetadataProvider|null $provider
     */
    public function __construct(InjectorConfig $config = null, InjectMetadataProvider $provider = null)
    {
        AnnotationReader::addGlobalIgnoredName('Inject');
        $this->injectorConfig = $config ?: new InjectorConfig();
        $this->bindings = new Bindings($this->injectorConfig, $this);
        $this->factory = new InstanceFactory(
            $this->bindings,
            $provider ?: new AttributesInjectMetadataProvider(),
            $this->injectorConfig->getEagerInstanceCreator(),
            $this->injectorConfig->getLazyInstanceCreator()
        );
        $this->repository = new InstanceRepository($this->bindings);
    }

    /** @return InjectorConfig */
    public function getInjectorConfig()
    {
        return $this->injectorConfig;
    }

    /**
     * @param string $className
     * @param string $name
     * @return object
     */
    public function getInstance($className, $name = '')
    {
        $binder = $this->bindings->getBinder($className, $name);
        return $this->repository->getInstance($this->factory, $binder);
    }
}
