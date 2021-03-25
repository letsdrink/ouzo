<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\RedirectHandler;

class MockRedirectHandler extends RedirectHandler
{
    private string $location;

    public function redirect(string $url): void
    {
        $this->location = $url;
    }

    public function getLocation(): string
    {
        return $this->location;
    }
}
