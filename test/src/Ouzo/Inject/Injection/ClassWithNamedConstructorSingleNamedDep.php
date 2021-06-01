<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Annotation\Named;

class ClassWithNamedConstructorSingleNamedDep
{
    public ClassWithPrivateDep $myClass;
    public ClassWithPrivateDep $secondClass;

    #[Inject]
    public function __construct(ClassWithPrivateDep $myClass, #[Named('my_second_dep')] ClassWithPrivateDep $secondClass)
    {
        $this->myClass = $myClass;
        $this->secondClass = $secondClass;
    }
}
