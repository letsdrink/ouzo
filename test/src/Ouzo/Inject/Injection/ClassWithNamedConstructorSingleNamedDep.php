<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Annotation\Named;

class ClassWithNamedConstructorSingleNamedDep
{
    public ClassWithPrivateDep $myClass;
    public ClassWithPrivateDep $secondClass;

    #[Inject]
    #[Named('my_second_dep', 'secondClass')]
    public function __construct(ClassWithPrivateDep $myClass, ClassWithPrivateDep $secondClass)
    {
        $this->myClass = $myClass;
        $this->secondClass = $secondClass;
    }
}
