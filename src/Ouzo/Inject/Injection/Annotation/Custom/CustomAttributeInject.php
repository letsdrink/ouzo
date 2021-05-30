<?php

namespace Ouzo\Injection\Annotation\Custom;

use ReflectionParameter;
use ReflectionProperty;

interface CustomAttributeInject
{
    /** @param ReflectionProperty[] $reflectionProperties */
    public function forProperties(object $instance, array $reflectionProperties): void;

    public function forConstructorParameter(ReflectionParameter $parameter): mixed;
}
