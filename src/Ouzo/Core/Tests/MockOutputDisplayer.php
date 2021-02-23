<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\OutputDisplayer;

class MockOutputDisplayer extends OutputDisplayer
{
    public function display(string $content): void
    {
        echo '';
    }
}
