<?php
namespace Ouzo\Db;

class RelationWithAlias
{
    public $relation;
    public $alias;

    public function __construct($relation, $alias)
    {
        $this->relation = $relation;
        $this->alias = $alias;
    }

    public function __toString()
    {
        return "{$this->relation} {$this->alias}";
    }
}