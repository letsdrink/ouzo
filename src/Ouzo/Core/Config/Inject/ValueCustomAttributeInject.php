<?php

namespace Ouzo\Config\Inject;

use Ouzo\Config;
use Ouzo\Injection\Annotation\Custom\CustomAttributeInject;
use Ouzo\Utilities\Strings;
use ReflectionAttribute;
use ReflectionParameter;

class ValueCustomAttributeInject implements CustomAttributeInject
{
    private const CONFIG_START = '${';
    private const CONFIG_END = '}';

    public function forProperties(object $instance, array $reflectionProperties): void
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

    public function forConstructorParameter(ReflectionParameter $parameter): mixed
    {
        $attributes = $parameter->getAttributes(Value::class);
        if (empty($attributes)) {
            return null;
        }

        return $this->getConfigValue($attributes[0]);
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
