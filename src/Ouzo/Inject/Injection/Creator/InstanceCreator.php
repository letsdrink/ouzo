<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Creator;

use Ouzo\Injection\Factory;
use Ouzo\Injection\InstanceFactory;
use Ouzo\Injection\InstanceRepository;

interface InstanceCreator
{
    public function create(string $className, ?array $arguments, InstanceRepository $repository, InstanceFactory $instanceFactory): object;

    public function createThroughFactory(string $className, ?array $arguments, InstanceRepository $repository, InstanceFactory $instanceFactory, Factory $factory): object;
}
