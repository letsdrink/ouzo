<?php

namespace Ouzo\Db;


class SelectColumnCallback
{
    private $prev_table;

    function __invoke($matches)
    {
        $table = $matches[1];
        if ($table != $this->prev_table) {
            $first = !$this->prev_table;
            $this->prev_table = $table;
            $result = "$table.*";
            return $first? $result : ", $result";
        }
        return "";
    }
}

class QueryHumanizer
{
    public static function humanize($sql)
    {
        return preg_replace_callback('/(\w+)\.\w+ AS \w+(, )?/', new SelectColumnCallback(), $sql);
    }
}