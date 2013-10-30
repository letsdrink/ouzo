<?php

namespace Ouzo\Db;

class RelationToFetch
{
    public $field;
    public $relation;

    function __construct($field, $relation)
    {
        $this->field = $field;
        $this->relation = $relation;
    }
} 