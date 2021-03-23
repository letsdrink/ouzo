<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing\Loader;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\Request\Annotation\ResponseCode;
use Ouzo\Routing\Annotation\Route;
use ReflectionClass;
use ReflectionMethod;

class AnnotationClassLoader implements Loader
{
    /** @var Reader */
    private $reader;

    #[Inject]
    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param array $classes
     * @return RouteMetadataCollection
     * @throws \ReflectionException
     */
    public function load(array $classes): RouteMetadataCollection
    {
        $collection = new RouteMetadataCollection();
        foreach ($classes as $class) {
            if (!class_exists($class)) {
                throw new InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
            }

            $reflectionClass = new ReflectionClass($class);
            $this->addRouteMetadata($collection, $reflectionClass);
        }
        return $collection;
    }

    /**
     * @param RouteMetadataCollection $collection
     * @param ReflectionClass $reflectionClass
     */
    private function addRouteMetadata(RouteMetadataCollection $collection, ReflectionClass $reflectionClass): void
    {
        $uriPrefix = '';
        if ($annotation = $this->reader->getClassAnnotation($reflectionClass, Route::class)) {
            $uriPrefix = $annotation->getPath();
        }

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $methodAnnotations = $this->reader->getMethodAnnotations($reflectionMethod);
            foreach ($methodAnnotations as $methodAnnotation) {
                if ($methodAnnotation instanceof Route) {
                    foreach ($methodAnnotation->getMethods() as $method) {
                        $collection->addRouteMetadata(new RouteMetadata(
                            $uriPrefix . $methodAnnotation->getPath(),
                            $method,
                            $reflectionClass->getName(),
                            $reflectionMethod->getName(),
                            $methodAnnotation->getResponseCode()
                        ));
                    }
                }
            }
        }
    }
}
