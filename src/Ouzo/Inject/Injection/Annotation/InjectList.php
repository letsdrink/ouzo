<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class InjectList
{
    public function __construct(private string $className, private ?string $name = null)
    {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
