<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\MyNamespace\ClassWithNamespace;
use Ouzo\Injection\Annotation\Inject;

class ClassWithTypedProperty
{
    /** @Inject */
    public ClassWithNamespace $myClass;

    /**
     * @Inject
     * @var ClassWithNoDep
     */
    public $mySecondClass;
}