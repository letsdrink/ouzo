<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Application\Model\Test;

use Ouzo\Routing\Annotation\Route\Delete;
use Ouzo\Routing\Annotation\Route\Get;
use Ouzo\Routing\Annotation\Route\Post;
use Ouzo\Routing\Annotation\Route\Put;

class CrudController
{
    #[Post('/create')]
    public function post()
    {
    }

    #[Get('/read')]
    public function get()
    {
    }

    #[Put('/update')]
    public function put()
    {
    }

    #[Delete('/delete')]
    public function delete()
    {
    }
}
