<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Annotation;

class AttributeInjectorRegistry
{
    private array $attributeInjectors = [];

    public function register(AttributeInjector $attributeInjector)
    {
        $this->attributeInjectors[] = $attributeInjector;
    }

    /** @return AttributeInjector[] */
    public function getAttributeInjectors(): array
    {
        return $this->attributeInjectors;
    }
}
