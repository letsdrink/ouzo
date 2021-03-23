<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

class OutputRenderer
{
    public function display(string $content): void
    {
        echo $content;
    }
}
