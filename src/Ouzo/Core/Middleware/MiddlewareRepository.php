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
    private $interceptors = [];

    /**
     * @param Interceptor $interceptor
     * @return $this
     */
    public function add(Interceptor $interceptor)
    {
        $this->interceptors[] = $interceptor;

        return $this;
    }

    /**
     * @param Interceptor[] $interceptors
     * @return $this
     */
    public function addAll(array $interceptors)
    {
        $this->interceptors = array_merge($this->interceptors, $interceptors);

        return $this;
    }

    /** @return Interceptor[] */
    public function getInterceptors()
    {
        return $this->interceptors;
    }
}
