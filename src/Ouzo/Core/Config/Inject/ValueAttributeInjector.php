<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Config\Inject;

use Ouzo\Config\ConfigValueSelector;
use Ouzo\Injection\Annotation\AttributeInjector;
use Ouzo\Injection\InstanceFactory;
use Ouzo\Utilities\Booleans;
use Ouzo\Utilities\Strings;
use ReflectionAttribute;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;

class ValueAttributeInjector implements AttributeInjector
{
    public function injectForProperties(object $instance, array $reflectionProperties, InstanceFactory $instanceFactory): void
    {
        foreach ($reflectionProperties as $reflectionProperty) {
            $attributes = $reflectionProperty->getAttributes(Value::class);
            if (!empty($attributes)) {
                $configValue = $this->getConfigValue($attributes[0], $reflectionProperty->getType());

                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($instance, $configValue);
            }
        }
    }

    public function injectForConstructorParameter(ReflectionMethod $constructor, InstanceFactory $instanceFactory): array
    {
        $constructorParameters = [];
        $parameters = $constructor->getParameters();
        foreach ($parameters as $parameter) {
            $attributes = $parameter->getAttributes(Value::class);
            if (!empty($attributes)) {
                $parameterName = $parameter->getName();
                $constructorParameters[$parameterName] = $this->getConfigValue($attributes[0], $parameter->getType());
            }
        }
        return $constructorParameters;
    }

    private function getConfigValue(ReflectionAttribute $attribute, ?ReflectionType $type): mixed
    {
        /** @var Value $value */
        $value = $attribute->newInstance();
        $selector = $value->getSelector();

        $configValue = ConfigValueSelector::selectConfigValue($selector);
        if (is_string($configValue) && Strings::isNotBlank($configValue) && $type instanceof ReflectionNamedType && $type->getName() === 'bool') {
            return Booleans::toBoolean($configValue);
        }
        return $configValue;
    }
}
