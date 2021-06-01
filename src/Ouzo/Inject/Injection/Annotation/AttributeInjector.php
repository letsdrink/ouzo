<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Annotation;

use Ouzo\Injection\InstanceFactory;
use ReflectionMethod;
use ReflectionProperty;

interface AttributeInjector
{
    /** @param ReflectionProperty[] $reflectionProperties */
    public function injectForProperties(object $instance, array $reflectionProperties, InstanceFactory $instanceFactory): void;

    public function injectForConstructorParameter(ReflectionMethod $constructor, InstanceFactory $instanceFactory): array;
}
