<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class HeaderSender
{
    public function send(array $headers): void
    {
        Arrays::map($headers, function ($header) {
            header($header);
        });
    }
}
