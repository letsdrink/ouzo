<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\FrontController;

class DefaultMessageFormatter implements MessageFormatter
{
    public function format(string $logger, string $level, string $message): string
    {
        return sprintf("%s %s: [ID: %s] %s", $logger, $level, FrontController::$requestId, $message);
    }
}
