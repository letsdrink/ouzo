<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\HeaderSender;

class MockHeaderSender extends HeaderSender
{
    private array $headers;

    public function send(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
