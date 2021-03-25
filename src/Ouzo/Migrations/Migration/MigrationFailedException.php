<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Exception;

class MigrationFailedException extends Exception
{
    /** @var Exception */
    private $exception;
    private $className;
    private $version;

    public function __construct(Exception $exception, $className, $version)
    {
        parent::__construct($exception->getMessage(), $exception->getCode(), $exception);
        $this->exception = $exception;
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