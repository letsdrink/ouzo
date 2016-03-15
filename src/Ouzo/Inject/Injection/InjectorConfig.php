<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection;

class InjectorConfig
{
    private $binders = array();

    public function bind($className, $name = '')
    {
        $binder = new Binder($className, $name);
        $this->binders[$className . '_' . $name] = $binder;
        return $binder;
    }

    public function getBinders()
    {
        return $this->binders;
    }
}
