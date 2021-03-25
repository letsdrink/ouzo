<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Utilities\Arrays;

class HeaderSender
{
    public function send(array $headers): void
    {
        Arrays::each($headers, fn($header) => header($header));
    }
}
