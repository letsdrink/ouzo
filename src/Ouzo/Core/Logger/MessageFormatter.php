<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

interface MessageFormatter
{
    public function format(string $logger, string $level, string $message): string;
}
