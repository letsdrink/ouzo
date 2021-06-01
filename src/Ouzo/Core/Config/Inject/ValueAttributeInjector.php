<?php

namespace Ouzo\Config\Inject;

use Ouzo\Config;
use Ouzo\Injection\Annotation\AttributeInjector;
use Ouzo\Injection\InstanceFactory;
use Ouzo\Utilities\Strings;
use ReflectionAttribute;
use ReflectionMethod;

class ValueAttributeInjector implements AttributeInjector
{
    private const CONFIG_START = '${';
    private const CONFIG_END = '}';

    public function injectForProperties(object $instance, array $reflectionProperties, InstanceFactory $instanceFactory): void
    {
        foreach ($reflectionProperties as $reflectionProperty) {
            $attributes = $reflectionProperty->getAttributes(Value::class);
            if (!empty($attributes)) {
                $configValue = $this->getConfigValue($attributes[0]);

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
                $constructorParameters[$parameterName] = $this->getConfigValue($attributes[0]);
            }
        }
        return $constructorParameters;
    }

    private function getConfigValue(ReflectionAttribute $attribute): mixed
    {
        /** @var Value $value */
        $value = $attribute->newInstance();
        $selector = $value->getSelector();

        if (Strings::startsWith($selector, self::CONFIG_START) && Strings::endsWith($selector, self::CONFIG_END)) {
            $selector = Strings::removePrefix($selector, self::CONFIG_START);
            $selector = Strings::removeSuffix($selector, self::CONFIG_END);
            $arguments = explode('.', $selector);
            return Config::getValue(...$arguments);
        }

        return $selector;
    }
}
