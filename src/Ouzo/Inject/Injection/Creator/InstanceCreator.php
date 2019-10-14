<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Creator;


use Ouzo\Injection\InstanceFactory;
use Ouzo\Injection\InstanceRepository;

interface InstanceCreator
{
    public function create(string $className, ?array $arguments, InstanceRepository $repository, InstanceFactory $instanceFactory);
}