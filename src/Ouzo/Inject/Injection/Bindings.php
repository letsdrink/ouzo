<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

class Bindings
{
    private Binder $binder;

    public function __construct(
        private InjectorConfig $injectorConfig,
        Injector $injector
    )
    {
        $binder = new Binder(Injector::class);
        $this->binder = $binder->toInstance($injector);
    }

    public function getBinder(string $className, string $name = ''): Binder
    {
        if ($className === Injector::class) {
            return $this->binder;
        }

        return $this->injectorConfig->getBinder($className, $name);
    }
}
