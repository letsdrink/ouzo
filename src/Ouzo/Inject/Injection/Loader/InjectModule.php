<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Loader;

use Ouzo\Injection\InjectorConfig;

interface InjectModule
{
    public function configureBindings(InjectorConfig $config);
}