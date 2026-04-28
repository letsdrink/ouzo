<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Utilities\Iterator;

use Closure;
use FilterIterator;
use Iterator;

/**
 * Class FilteringIterator
 * @package Ouzo\Utilities\Iterator
 *
 * For php 5.3 which does not support CallbackFilterIterator :(
 */
class FilteringIterator extends FilterIterator
{
    private Closure $predicate;

    #[Override]
    public function __construct(Iterator $iterator, callable $predicate)
    {
        parent::__construct($iterator);
        $this->predicate = Closure::fromCallable($predicate);
    }

    #[Override]
    public function accept(): bool
    {
        $predicate = $this->predicate;
        return $predicate($this->current());
    }
}
