<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Utilities\Iterator;

use Closure;
use Iterator;

class TransformingIterator extends ForwardingIterator
{
    private Closure $function;

    #[Override]
    public function __construct(Iterator $iterator, callable $function)
    {
        parent::__construct($iterator);
        $this->function = Closure::fromCallable($function);
    }

    #[Override]
    public function current(): mixed
    {
        return call_user_func($this->function, $this->iterator->current());
    }
}
