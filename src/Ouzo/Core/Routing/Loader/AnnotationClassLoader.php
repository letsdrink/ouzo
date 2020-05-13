<?php

namespace Ouzo\Routing\Loader;

use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use Ouzo\Routing\Annotation\Route;
use ReflectionClass;
use ReflectionMethod;

class AnnotationClassLoader
{
    private $reader;
    private $routeMetadataCollection;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        $this->routeMetadataCollection = new RouteMetadataCollection();
    }

    public function load(string $class): RouteMetadataCollection
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $reflectionClass = new ReflectionClass($class);
        $this->addRouteMetadata($reflectionClass);

        return $this->routeMetadataCollection;
    }

    private function addRouteMetadata(ReflectionClass $reflectionClass)
    {
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $methodAnnotations = $this->reader->getMethodAnnotations($reflectionMethod);
            foreach ($methodAnnotations as $methodAnnotation) {
                if ($methodAnnotation instanceof Route) {
                    foreach ($methodAnnotation->getMethods() as $method) {
                        $this->routeMetadataCollection->addRouteMetadata(new RouteMetadata(
                            $methodAnnotation->getPath(),
                            $method,
                            $reflectionClass->getName(),
                            $reflectionMethod->getName()
                        ));
                    }
                }
            }
        }
    }
}