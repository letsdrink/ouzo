<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use ArrayIterator;

class EmptyQueryExecutor
{
    /**
     * @return null
     */
    public function fetch()
    {
        return null;
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        return [];
    }

    /**
     * @return ArrayIterator
     */
    public function fetchIterator()
    {
        return new ArrayIterator([]);
    }

    /**
     * @return int
     */
    public function execute()
    {
        return 0;
    }

    /**
     * @return int
     */
    public function count()
    {
        return 0;
    }
}
