<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Annotation\Inject;

class ClassWithPrivateDep
{
    #[Inject]
    private ClassWithNoDep $myClass;

    public function getMyClass(): ClassWithNoDep
    {
        return $this->myClass;
    }
}
