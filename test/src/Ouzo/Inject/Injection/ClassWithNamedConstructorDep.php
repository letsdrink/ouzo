<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

class ClassWithNamedConstructorDep
{
    public $myClass;

    /**
     * @Inject @Named("my_dep")
     */
    public function __construct(ClassWithNoDep $myClass)
    {
        $this->myClass = $myClass;
    }
}