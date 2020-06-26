<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

use Doctrine\Common\Annotations\AnnotationReader;
use Ouzo\Injection\Annotation\AnnotationMetadataProvider;
use Ouzo\Injection\Annotation\DocCommentExtractor;

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
     * @param AnnotationMetadataProvider|null $provider
     */
    public function __construct(InjectorConfig $config = null, AnnotationMetadataProvider $provider = null)
    {
        AnnotationReader::addGlobalIgnoredName('Inject');
        $this->injectorConfig = $config ?: new InjectorConfig();
        $this->bindings = new Bindings($this->injectorConfig, $this);
        $this->factory = new InstanceFactory(
            $this->bindings,
            $provider ?: new DocCommentExtractor(),
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
