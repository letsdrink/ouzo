<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Utilities\Iterator;

use Closure;
use Iterator;

class GeneratingIterator implements Iterator
{
    private int $index;
    private mixed $current;
    private bool $initialized;
    private Closure $function;

    public function __construct(callable $function)
    {
        $this->function = Closure::fromCallable($function);
    }

    #[Override]
    public function current(): mixed
    {
        if (!$this->initialized) {
            $this->generate();
            $this->initialized = true;
        }
        return $this->current;
    }

    #[Override]
    public function valid(): bool
    {
        return true;
    }

    #[Override]
    public function key(): int
    {
        return $this->index;
    }

    #[Override]
    public function next(): void
    {
        $this->index++;
        $this->generate();
    }

    #[Override]
    public function rewind(): void
    {
        $this->index = 0;
        $this->initialized = false;
    }

    private function generate(): void
    {
        $function = $this->function;
        $this->current = $function($this->index);
    }
}
