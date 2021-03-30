<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Application\Model\Test;

use Ouzo\Routing\Annotation\Route\Get;
use Ouzo\Routing\Annotation\Route\Post;
use Ouzo\Routing\Annotation\RoutePrefix;

#[RoutePrefix('/prefix')]
class GlobalController
{
    #[Get('/')]
    public function index()
    {
    }

    #[Post('/action')]
    public function action()
    {
    }
}
