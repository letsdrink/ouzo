<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Utilities\Iterator;

use Iterator;

class ForwardingIterator implements Iterator
{
    public function __construct(protected Iterator $iterator)
    {
    }

    #[Override]
    public function current(): mixed
    {
        return $this->iterator->current();
    }

    #[Override]
    public function next(): void
    {
        $this->iterator->next();
    }

    #[Override]
    public function key(): mixed
    {
        return $this->iterator->key();
    }

    #[Override]
    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    #[Override]
    public function rewind(): void
    {
        $this->iterator->rewind();
    }
}
