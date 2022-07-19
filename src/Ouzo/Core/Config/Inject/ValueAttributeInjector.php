<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Config\Inject;

use Ouzo\Config\ConfigValueSelector;
use Ouzo\Injection\Annotation\AttributeInjector;
use Ouzo\Injection\InjectorException;
use Ouzo\Injection\InstanceFactory;
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
                $configValue = $this->getConfigValue($attributes[0], $reflectionProperty->getName(), $reflectionProperty->getType(), $instance::class);

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
                $constructorParameters[$parameterName] = $this->getConfigValue($attributes[0], $parameter->getName(), $parameter->getType(), $constructor->class);
            }
        }
        return $constructorParameters;
    }

    private function getConfigValue(ReflectionAttribute $attribute, string $parameterName, ?ReflectionType $type, string $class): mixed
    {
        /** @var Value $value */
        $value = $attribute->newInstance();
        $selector = $value->getSelector();

        $configValue = ConfigValueSelector::selectConfigValue($selector);
        if (is_string($configValue) && $type instanceof ReflectionNamedType) {
            return match ($type->getName()) {
                'bool' => $this->handleBoolValue($configValue, $parameterName, $class, $selector),
                'int' => $this->handleIntValue($configValue, $parameterName, $class, $selector),
                default => $configValue
            };
        }
        return $configValue;
    }

    private function handleBoolValue(string $value, string $parameterName, string $class, string $selector): bool
    {
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }
        throw $this->getInjectorException('bool', $parameterName, $class, $value, $selector);
    }

    private function handleIntValue(string $value, string $parameterName, string $class, string $selector): int
    {
        if (ctype_digit($value)) {
            return intval($value);
        }
        throw $this->getInjectorException('int', $parameterName, $class, $value, $selector);
    }

    private function getInjectorException(string $type, string $parameterName, string $class, string $value, string $selector): InjectorException
    {
        return new InjectorException("Cannot inject #[Value] to `{$parameterName}` for class `$class`. Invalid {$type} value: `{$value}` for selector: `{$selector}`");
    }
}
