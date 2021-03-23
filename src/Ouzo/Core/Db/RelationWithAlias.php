<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

class RelationWithAlias
{
    public function __construct(public Relation $relation, public ?string $alias)
    {
    }

    public function __toString(): string
    {
        return "{$this->relation} {$this->alias}";
    }
}
