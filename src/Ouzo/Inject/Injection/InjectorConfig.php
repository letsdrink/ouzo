<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;


use Ouzo\Utilities\Arrays;

class InjectorConfig
{
    private $binders = array();

    public function bind($className)
    {
        $binder = new Binder($className);
        $this->binders[$className] = $binder;
        return $binder;
    }

    public function getBinder($className)
    {
        $binder = Arrays::getValue($this->binders, $className);
        return $binder ?: new Binder($className);
    }
}