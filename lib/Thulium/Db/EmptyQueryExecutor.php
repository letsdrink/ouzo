<?php

namespace Thulium\Db;


class EmptyQueryExecutor {


    public function fetch()
    {
        return null;
    }

    public function fetchAll()
    {
        return array();
    }

    public function delete()
    {
        return 0;
    }

    public function count()
    {
        return 0;
    }
}