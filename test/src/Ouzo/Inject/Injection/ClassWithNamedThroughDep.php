<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Annotation\Named;

class ClassWithNamedThroughDep
{
    /**
     * @Inject
     * @Named("through_dep")
     * @var \ClassWithNoDep
     */
    public $myClass;
}