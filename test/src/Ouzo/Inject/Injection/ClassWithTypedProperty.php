<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\MyNamespace\ClassWithNamespace;
use Ouzo\Injection\Annotation\Inject;

class ClassWithTypedProperty
{
    #[Inject]
    public ClassWithNamespace $myClass;

    #[Inject]
    public ClassWithNoDep $mySecondClass;
}
