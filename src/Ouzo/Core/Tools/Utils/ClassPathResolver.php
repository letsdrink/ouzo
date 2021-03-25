<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Utils;

use Ouzo\Utilities\Path;

class ClassPathResolver
{
    private function __construct(private string $className, private string $nameSpace)
    {
    }

    public static function forClassAndNamespace(string $className, ?string $nameSpace = null): self
    {
        return new self($className, $nameSpace);
    }

    private function resolvePathFromNameSpace(): string
    {
        $parts = explode('\\', $this->nameSpace);
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    public function getClassFileName(): string
    {
        return Path::join(ROOT_PATH, $this->resolvePathFromNameSpace(), "{$this->className}.php");
    }

    public function getClassDirectory(): string
    {
        return Path::join(ROOT_PATH, $this->resolvePathFromNameSpace(), $this->className);
    }
}
