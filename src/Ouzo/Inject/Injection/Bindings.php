<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

class Bindings
{
    /** @var InjectorConfig */
    private $config;
    /** @var Binder */
    private $injectorBinder;

    /**
     * @param InjectorConfig $config
     * @param object $injector
     */
    public function __construct(InjectorConfig $config, $injector)
    {
        $this->config = $config;
        $binder = new Binder(Injector::class);
        $this->injectorBinder = $binder->toInstance($injector);
    }

    /**
     * @param string $className
     * @param string $name
     * @return Binder
     */
    public function getBinder($className, $name = '')
    {
        if ($className == Injector::class) {
            return $this->injectorBinder;
        }
        return $this->config->getBinder($className, $name);
    }
}
