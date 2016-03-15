<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;

use Ouzo\Injection\Annotation\AnnotationMetadataProvider;
use Ouzo\Injection\Annotation\DocCommentExtractor;

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

    public function __construct(InjectorConfig $config = null, AnnotationMetadataProvider $provider = null)
    {
        $this->config = $config ?: new InjectorConfig();
        $this->factory = new InstanceFactory($this->config, $provider ?: new DocCommentExtractor());
        $this->repository = new InstanceRepository();
    }

    public function getInstance($className, $name = '')
    {
        if ($className == '\Ouzo\Injection\Injector') {
            return $this;
        }
        $binder = $this->config->getBinder($className, $name);
        return $this->repository->getInstance($this->factory, $binder);
    }
}
