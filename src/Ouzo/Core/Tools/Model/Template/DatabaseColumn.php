<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Model\Template;

class DatabaseColumn
{
    public function __construct(
        public string $name,
        public string $type,
        public ?string $default = ''
    )
    {
    }
}
