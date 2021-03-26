<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Annotation;

use ReflectionClass;

interface InjectMetadataProvider
{
    public function getMetadata(ReflectionClass $class, bool $privatePropertiesOnly = false): array;

    public function getConstructorMetadata(string $className): array;
}
