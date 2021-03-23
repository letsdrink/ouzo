<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Annotation\Inject;

class ClassWithConstructorDep
{
    public ClassWithNoDep $myClass;

    #[Inject]
    public function __construct(ClassWithNoDep $myClass)
    {
        $this->myClass = $myClass;
    }
}
