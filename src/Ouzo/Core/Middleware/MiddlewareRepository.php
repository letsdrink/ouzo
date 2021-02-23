<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Middleware;

use Ouzo\Utilities\Chain\Interceptor;

class MiddlewareRepository
{
    /** @var Interceptor[] */
    private array $interceptors = [];

    public function add(Interceptor $interceptor): static
    {
        $this->interceptors[] = $interceptor;

        return $this;
    }

    /** @param Interceptor[] $interceptors */
    public function addAll(array $interceptors): static
    {
        $this->interceptors = array_merge($this->interceptors, $interceptors);

        return $this;
    }

    /** @return Interceptor[] */
    public function getInterceptors(): array
    {
        return $this->interceptors;
    }
}
