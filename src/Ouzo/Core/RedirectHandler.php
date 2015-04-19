<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

class RedirectHandler
{
    public function redirect($url)
    {
        header('Location: ' . $url);
    }
}
