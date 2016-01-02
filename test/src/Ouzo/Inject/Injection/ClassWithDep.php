<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

class ClassWithDep
{
    /**
     * @Inject
     * @var \ClassWithNoDep
     */
    public $myClass;
}