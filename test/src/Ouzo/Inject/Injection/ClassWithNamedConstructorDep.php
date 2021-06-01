<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Annotation\Named;

class ClassWithNamedConstructorDep
{
    public ClassWithNoDep $myClass;

    #[Inject]
    public function __construct(#[Named('my_dep')] ClassWithNoDep $myClass)
    {
        $this->myClass = $myClass;
    }
}
