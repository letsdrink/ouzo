<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\SessionInitializer;

class MockSessionInitializer extends SessionInitializer
{
    public function startSession(): void
    {
        $_SESSION = isset($_SESSION) ? $_SESSION : [];
    }
}
