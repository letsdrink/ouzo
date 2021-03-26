<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Throwable;

class StackTrace
{
    public function __construct(
        private string $file,
        private int $line,
        private $trace = null)
    {
    }

    public static function forException(Throwable $exception): StackTrace
    {
        return new self($exception->getFile(), $exception->getLine(), $exception->getTraceAsString());
    }

    public function getTraceAsString(): string
    {
        return $this->trace;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLine(): string
    {
        return $this->line;
    }
}