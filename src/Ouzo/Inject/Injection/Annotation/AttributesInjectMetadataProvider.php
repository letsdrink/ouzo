<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Annotation;

use Ouzo\Injection\InjectorException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Strings;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

class AttributesInjectMetadataProvider implements InjectMetadataProvider
{
    private const ALL_PROPERTIES = ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED;

    public function getMetadata(ReflectionClass $class, bool $privatePropertiesOnly = false): array
    {
        $annotations = [];

        $properties = $class->getProperties($privatePropertiesOnly ? ReflectionProperty::IS_PRIVATE : self::ALL_PROPERTIES);
        foreach ($properties as $property) {
            $attributes = $property->getAttributes(Inject::class);
            $named = $property->getAttributes(Named::class);
            $named = !empty($named) ? $named[0] : null;

            if (!empty($attributes)) {
                $name = $named?->newInstance()->getName() ?: '';
                if (!$property->hasType()) {
                    throw new InjectorException("Cannot #[Inject] dependency - missing type. " .
                        "Use typed property \${$property->getName()} in class {$class->getName()}.");
                }
                $className = $property->getType()->getName();

                $annotations[$property->getName()] = ['name' => $name, 'className' => $className];
            }
        }

        return $annotations;
    }

    public function getConstructorMetadata(string $className): array
    {
        $annotations = [];

        $instance = new ReflectionClass($className);
        $constructor = $instance->getConstructor();
        if (!is_null($constructor)) {
            $attributes = $constructor->getAttributes(Inject::class);
            if (!empty($attributes)) {
                $parameters = $constructor->getParameters();
                $namedMap = $this->extractNamedMapNew($constructor);
                foreach ($parameters as $parameter) {
                    $type = $parameter->getType();
                    if (is_null($type) || !($type instanceof ReflectionNamedType)) {
                        throw new InjectorException("Cannot #[Inject] by constructor for class {$className}. " .
                            "All arguments should have types defined (but not union types!).");
                    }
                    $parameterName = $parameter->getName();
                    $name = Arrays::getValue($namedMap, $parameterName, '');

                    $annotations[$parameterName] = ['name' => $name, 'className' => $type->getName(), 'parameter' => $parameter];
                }
            }
        }

        return $annotations;
    }

    private function extractNamedMapNew(ReflectionMethod $constructor): array
    {
        $attributes = $constructor->getAttributes(Named::class);
        $parameters = $constructor->getParameters();

        $map = [];

        $nameToParameterMap = Arrays::toMap(
            $parameters,
            fn(ReflectionParameter $parameter) => $parameter->getName(),
            Functions::identity()
        );

        foreach ($attributes as $i => $attribute) {
            /** @var Named $named */
            $named = $attribute->newInstance();
            $parameterName = $named->getParameterName();
            $parameter = Strings::isBlank($parameterName) ? $parameters[$i] : $nameToParameterMap[$parameterName];

            $map = $map + [$parameter->getName() => $named->getName()];
        }

        return $map;
    }
}
