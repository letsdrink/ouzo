<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;

class Injector
{

    /**
     * @var InjectorConfig
     */
    private $config;
    /**
     * @var InstanceRepository
     */
    private $repository;
    /**
     * @var InstanceFactory
     */
    private $factory;

    function __construct(InjectorConfig $config = null)
    {
        $this->config = $config ?: new InjectorConfig();
        $this->factory = new InstanceFactory($this->config);
        $this->repository = new InstanceRepository();
    }

    public function getInstance($className)
    {
        $binder = $this->config->getBinder($className);
        return $this->repository->getInstance($this->factory, $binder);
    }
}