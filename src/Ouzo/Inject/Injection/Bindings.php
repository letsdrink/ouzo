<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;


use Ouzo\Utilities\Arrays;

class Bindings
{
    private $binders;

    public function __construct($binders, $injector)
    {
        $this->binders = $binders;
        $injectorClass = '\Ouzo\Injection\Injector';
        $this->binders[$injectorClass . '_'] = (new Binder($injectorClass))->toInstance($injector);
    }

    public function getBinder($className, $name)
    {
        $binder = Arrays::getValue($this->binders, $className . '_' . $name);
        return $binder ?: new Binder($className, $name);
    }
}