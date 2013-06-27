<?php

namespace Thulium\Db;


class EmptyQueryExecutor {


    public function fetch()
    {
        return array();
    }

    public function fetchAll()
    {
        return array();
    }

    public function delete()
    {
        return 0;
    }

    public function fetchFirst()
    {
        return 0;
    }

    public function count()
    {
        return 0;
    }
}