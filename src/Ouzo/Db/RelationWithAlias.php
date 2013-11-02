<?php
namespace Ouzo\Db;

class RelationWithAlias
{
    public $relation;
    public $alias;

    function __construct($relation, $alias)
    {
        $this->relation = $relation;
        $this->alias = $alias;
    }

    function __toString()
    {
        return "{$this->relation} {$this->alias}";
    }
} 