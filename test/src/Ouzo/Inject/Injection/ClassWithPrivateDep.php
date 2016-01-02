<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

class ClassWithPrivateDep
{
    /**
     * @Inject
     * @var ClassWithNoDep
     */
    private $myClass;

    /**
     * @return ClassWithNoDep
     */
    public function getMyClass()
    {
        return $this->myClass;
    }
}