<?php
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
