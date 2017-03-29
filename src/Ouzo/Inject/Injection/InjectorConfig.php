<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class InjectorConfig
{
    private $binders = [];

    public function bind($className, $name = '')
    {
//        $className = Strings::prependIfMissing($className, '\\');
        $binder = new Binder($className, $name);
        $this->binders[$className . '_' . $name] = $binder;
        return $binder;
    }

    public function bindInstance($instance, $name = '')
    {
        return $this->bind(get_class($instance), $name)->toInstance($instance);
    }

    public function getBinder($className, $name)
    {
        $binder = Arrays::getValue($this->binders, $className . '_' . $name);
        return $binder ?: new Binder($className, $name);
    }
}
