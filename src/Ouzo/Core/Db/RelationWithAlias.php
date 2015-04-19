<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
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
