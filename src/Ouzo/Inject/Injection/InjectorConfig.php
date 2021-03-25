<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection;

use Ouzo\Injection\Creator\EagerInstanceCreator;
use Ouzo\Injection\Creator\InstanceCreator;
use Ouzo\Utilities\Arrays;

class InjectorConfig
{
    private InstanceCreator $lazyInstanceCreator;
    private InstanceCreator $eagerInstanceCreator;
    /** @var Binder[] */
    private array $binders = [];

    public function __construct()
    {
        $this->lazyInstanceCreator = $this->eagerInstanceCreator = new EagerInstanceCreator();
    }

    public function bind(string $className, string $name = ''): Binder
    {
        $binder = new Binder($className, $name);
        $this->binders[$className . '_' . $name] = $binder;
        return $binder;
    }

    public function bindInstance(object $instance, string $name = ''): Binder
    {
        return $this->bind($instance::class, $name)->toInstance($instance);
    }

    public function getBinder(string $className, string $name): Binder
    {
        $binder = Arrays::getValue($this->binders, $className . '_' . $name);
        return $binder ?: new Binder($className, $name);
    }

    public function getLazyInstanceCreator(): InstanceCreator
    {
        return $this->lazyInstanceCreator;
    }

    public function setLazyInstanceCreator(InstanceCreator $lazyInstanceCreator): void
    {
        $this->lazyInstanceCreator = $lazyInstanceCreator;
    }

    public function getEagerInstanceCreator(): InstanceCreator
    {
        return $this->eagerInstanceCreator;
    }

    public function setEagerInstanceCreator(InstanceCreator $eagerInstanceCreator): void
    {
        $this->eagerInstanceCreator = $eagerInstanceCreator;
    }
}
