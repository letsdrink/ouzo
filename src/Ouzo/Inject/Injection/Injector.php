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
     * @var InstanceRepository
     */
    private $repository;
    /**
     * @var InstanceFactory
     */
    private $factory;

    public function __construct(InjectorConfig $config = null, AnnotationMetadataProvider $provider = null)
    {
        $config = $config ?: new InjectorConfig();
        $this->bindings = new Bindings($config->getBinders(), $this) ;
        $this->factory = new InstanceFactory($this->bindings, $provider ?: new DocCommentExtractor());
        $this->repository = new InstanceRepository();
    }

    public function getInstance($className, $name = '')
    {
        $binder = $this->bindings->getBinder($className, $name);
        return $this->repository->getInstance($this->factory, $binder);
    }
}
