<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Application\Model\Test;

use Ouzo\Routing\Annotation\Route\Get;
use Ouzo\Routing\Annotation\Route\Post;

class MultipleMethods
{
    #[Get('/get')]
    #[Post('/post')]
    public function getAndPost()
    {
    }
}
