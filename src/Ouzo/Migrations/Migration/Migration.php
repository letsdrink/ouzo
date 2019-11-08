<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Ouzo\Db;

abstract class Migration
{
    public abstract function run(Db $db);
}