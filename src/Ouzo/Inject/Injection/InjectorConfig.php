<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;

use Ouzo\Utilities\Arrays;

class InjectorConfig
{
    /** @var Binder[] */
    private $binders = [];

    /**
     * @param string $className
     * @param string $name
     * @return Binder
     */
    public function bind($className, $name = '')
    {
        $binder = new Binder($className, $name);
        $this->binders[$className . '_' . $name] = $binder;
        return $binder;
    }

    /**
     * @param string $instance
     * @param string $name
     * @return Binder
     */
    public function bindInstance($instance, $name = '')
    {
        return $this->bind(get_class($instance), $name)->toInstance($instance);
    }

    /**
     * @param string $className
     * @param string $name
     * @return Binder
     */
    public function getBinder($className, $name)
    {
        $binder = Arrays::getValue($this->binders, $className . '_' . $name);
        return $binder ?: new Binder($className, $name);
    }
}
