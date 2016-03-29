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

    public function bind($className, $name = '')
    {
        $binder = new Binder($className, $name);
        $this->binders[$className . '_' . $name] = $binder;
        return $binder;
    }

    public function bindInstance($instance, $name = '')
    {
        $className = '\\' . get_class($instance);
        return $this->bind($className, $name)->toInstance($instance);
    }

    public function getBinder($className, $name)
    {
        $binder = Arrays::getValue($this->binders, $className . '_' . $name);
        return $binder ?: new Binder($className, $name);
    }
}
