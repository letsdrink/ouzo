<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Exception;
use Throwable;

class MigrationFailedException extends Exception
{
    /** @var Throwable */
    private $throwable;
    private $className;
    private $version;

    public function __construct(Throwable $throwable, $className, $version)
    {
        parent::__construct($throwable->getMessage(), $throwable->getCode(), $throwable);
        $this->throwable = $throwable;
        $this->className = $className;
        $this->version = $version;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getVersion()
    {
        return $this->version;
    }
}