<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class HeaderSender
{
    /**
     * @param array $headers
     */
    public function send($headers)
    {
        Arrays::map($headers, function ($header) {
            header($header);
        });
    }
}
