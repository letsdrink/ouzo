<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Annotation\Named;

class ClassWithNamedConstructorMultipleDep
{
    public ClassWithNoDep $myClass;
    public ClassWithPrivateDep $secondClass;

    #[Inject]
    #[Named('my_dep', 'myClass')]
    #[Named('my_second_dep', 'secondClass')]
    public function __construct(ClassWithNoDep $myClass, ClassWithPrivateDep $secondClass)
    {
        $this->myClass = $myClass;
        $this->secondClass = $secondClass;
    }
}
