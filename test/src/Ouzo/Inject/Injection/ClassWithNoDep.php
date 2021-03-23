<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

class ClassWithNoDep
{
    private bool $throughFactoryFlag = false;

    public function someMethod()
    {
    }

    public function isThroughFactoryFlag(): bool
    {
        return $this->throughFactoryFlag;
    }

    public function setThroughFactoryFlag(): ClassWithNoDep
    {
        $this->throughFactoryFlag = true;
        return $this;
    }
}
