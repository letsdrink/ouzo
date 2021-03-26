<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Named
{
    public function __construct(private string $name, private ?string $parameterName = null)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParameterName(): ?string
    {
        return $this->parameterName;
    }
}
