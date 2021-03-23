<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use ArrayIterator;
use Ouzo\Model;

class EmptyQueryExecutor
{
    public function fetch(): ?Model
    {
        return null;
    }

    /** @return Model[] */
    public function fetchAll(): array
    {
        return [];
    }

    public function fetchIterator(): ArrayIterator
    {
        return new ArrayIterator([]);
    }

    public function execute(): int
    {
        return 0;
    }

    public function count(): int
    {
        return 0;
    }
}
