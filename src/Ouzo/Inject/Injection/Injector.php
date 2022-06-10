<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

use Ouzo\Injection\Annotation\AttributeInjectorRegistry;
use Ouzo\Injection\Annotation\InjectAttributeInjector;
use Ouzo\Injection\Annotation\InjectListAttributeInjector;

class Injector
{
    private InjectorConfig $injectorConfig;
    private Bindings $bindings;
    private InstanceFactory $instanceFactory;
    private InstanceRepository $instanceRepository;

    public function __construct(
        InjectorConfig $injectorConfig = null,
        AttributeInjectorRegistry $attributeInjectorRegistry = null
    )
    {
        $this->injectorConfig = $injectorConfig ?: new InjectorConfig();
        $this->bindings = new Bindings($this->injectorConfig, $this);
        $this->instanceRepository = new InstanceRepository($this->bindings);

        $attributeInjectorRegistry = $attributeInjectorRegistry ?: new AttributeInjectorRegistry();
        $attributeInjectorRegistry->register(new InjectAttributeInjector($this->bindings, $this->instanceRepository));
        $attributeInjectorRegistry->register(new InjectListAttributeInjector($this->bindings, $this->instanceRepository));

        $this->instanceFactory = new InstanceFactory(
            $this->injectorConfig->getEagerInstanceCreator(),
            $this->injectorConfig->getLazyInstanceCreator(),
            $attributeInjectorRegistry
        );
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
