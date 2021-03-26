<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\OutputRenderer;

class MockOutputRenderer extends OutputRenderer
{
    public function display(string $content): void
    {
        echo '';
    }
}
