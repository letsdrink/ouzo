<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

class ClassWithNamedDep
{
    /**
     * @Inject @Named("my_dep")
     * @var \ClassWithNoDep
     */
    public $myClass;
}