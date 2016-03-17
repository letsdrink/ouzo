<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;

class Bindings
{
    private $config;
    private $injectorBinder;

    public function __construct(InjectorConfig $config, $injector)
    {
        $this->config = $config;
        $binder = new Binder('\Ouzo\Injection\Injector');
        $this->injectorBinder = $binder->toInstance($injector);
    }

    /**
     * @param $className
     * @param $name
     * @return Binder
     */
    public function getBinder($className, $name)
    {
        if ($className == '\Ouzo\Injection\Injector') {
            return $this->injectorBinder;
        }
        return $this->config->getBinder($className, $name);
    }
}
