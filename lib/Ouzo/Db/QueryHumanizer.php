<?php

namespace Ouzo\Db;

class QueryHumanizer
{
    public static function humanize($sql)
    {
        $noDuplicates = preg_replace('/, (\w+)\.\w+ AS \w+(?=,)/', '', $sql);
        return preg_replace('/(\w+)\.\w+ AS \w+/', '$1.*', $noDuplicates);
    }
}