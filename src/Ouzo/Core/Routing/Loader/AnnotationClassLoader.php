<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing\Loader;

use InvalidArgumentException;
use Ouzo\Routing\Annotation\Route;
use Ouzo\Routing\Annotation\RoutePrefix;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

class AnnotationClassLoader implements Loader
{
    public function load(array $classes): RouteMetadataCollection
    {
        $collection = new RouteMetadataCollection();
        foreach ($classes as $class) {
            if (!class_exists($class)) {
                throw new InvalidArgumentException("Class '{$class}' does not exist.");
            }

            $reflectionClass = new ReflectionClass($class);
            $this->addRouteMetadata($collection, $reflectionClass);
        }
        return $collection;
    }

    private function addRouteMetadata(RouteMetadataCollection $collection, ReflectionClass $reflectionClass): void
    {
        $uriPrefix = '';
        $attributesForClass = $reflectionClass->getAttributes(RoutePrefix::class);
        if (!empty($attributesForClass)) {
            /** @var RoutePrefix $routePrefix */
            $routePrefix = $attributesForClass[0]->newInstance();
            $uriPrefix = $routePrefix->getPrefix();
        }

        $reflectionMethods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($reflectionMethods as $reflectionMethod) {
            $attributesForMethod = $reflectionMethod->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF);

            foreach ($attributesForMethod as $attributeForMethod) {
                /** @var Route $route */
                $route = $attributeForMethod->newInstance();

                foreach ($route->getHttpMethods() as $httpMethod) {
                    $collection->addRouteMetadata(new RouteMetadata(
                        $uriPrefix . $route->getPath(),
                        $httpMethod,
                        $reflectionClass->getName(),
                        $reflectionMethod->getName(),
                        $route->getHttpResponseCode()
                    ));
                }
            }
        }
    }
}
