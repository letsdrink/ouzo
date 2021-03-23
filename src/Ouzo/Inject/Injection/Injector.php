<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

use Ouzo\Injection\Annotation\AttributesInjectMetadataProvider;
use Ouzo\Injection\Annotation\InjectMetadataProvider;

class Injector
{
    private InjectorConfig $injectorConfig;
    private Bindings $bindings;
    private InstanceFactory $instanceFactory;
    private InstanceRepository $instanceRepository;

    public function __construct(InjectorConfig $injectorConfig = null, InjectMetadataProvider $injectMetadataProvider = null)
    {
        $this->injectorConfig = $injectorConfig ?: new InjectorConfig();
        $this->bindings = new Bindings($this->injectorConfig, $this);
        $this->instanceFactory = new InstanceFactory(
            $this->bindings,
            $injectMetadataProvider ?: new AttributesInjectMetadataProvider(),
            $this->injectorConfig->getEagerInstanceCreator(),
            $this->injectorConfig->getLazyInstanceCreator()
        );
        $this->instanceRepository = new InstanceRepository($this->bindings);
    }

    public function getInjectorConfig(): InjectorConfig
    {
        return $this->injectorConfig;
    }

    public function getInstance(string $className, string $name = ''): object
    {
        $binder = $this->bindings->getBinder($className, $name);
        return $this->instanceRepository->getInstance($this->instanceFactory, $binder);
    }
}
