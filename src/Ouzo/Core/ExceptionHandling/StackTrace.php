<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Throwable;

class StackTrace
{
    private $file;
    private $line;
    private $trace;

    public function __construct($file, $line, $trace = null)
    {
        $this->file = $file;
        $this->line = $line;
        $this->trace = $trace;
    }

    /**
     * @param Throwable $exception
     * @return StackTrace
     */
    public static function forException($exception)
    {
        return new self($exception->getFile(), $exception->getLine(), $exception->getTraceAsString());
    }

    public function getTraceAsString()
    {
        return $this->trace;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getLine()
    {
        return $this->line;
    }
}