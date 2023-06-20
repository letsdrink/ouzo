<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Application\Model\Test;

use Ouzo\Routing\Annotation\Route\Get;

class RepeatedMethods
{
    #[Get('/get1')]
    #[Get('/get2')]
    public function getAndPost()
    {
    }
}
