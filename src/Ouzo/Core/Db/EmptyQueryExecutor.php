<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class EmptyQueryExecutor
{
    public function fetch()
    {
        return null;
    }

    public function fetchAll()
    {
        return array();
    }

    public function execute()
    {
        return 0;
    }

    public function count()
    {
        return 0;
    }
}
