<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tools\Model\Template;

class DatabaseColumn
{
    public $name;
    public $type;
    public $default;

    public function __construct($name, $type, $default = '')
    {
        $this->name = $name;
        $this->type = $type;
        $this->default = $default;
    }
}
