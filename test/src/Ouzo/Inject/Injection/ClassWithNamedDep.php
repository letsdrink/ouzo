<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Annotation\Named;

class ClassWithNamedDep
{
    #[Inject]
    #[Named('my_dep')]
    public ClassWithNoDep $myClass;
}
