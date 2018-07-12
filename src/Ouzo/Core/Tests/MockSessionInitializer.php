<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\SessionInitializer;

class MockSessionInitializer extends SessionInitializer
{
    public function startSession()
    {
        $_SESSION = isset($_SESSION) ? $_SESSION : [];
    }
}
