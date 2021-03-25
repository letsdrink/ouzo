<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RoutePrefix
{
    public function __construct(private string $prefix)
    {
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
