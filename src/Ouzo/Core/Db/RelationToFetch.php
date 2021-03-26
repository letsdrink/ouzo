<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Closure;

class RelationToFetch
{
    public function __construct(
        public string $field,
        public Relation $relation,
        public string $destinationField
    )
    {
    }

    public function equals(RelationToFetch $other): bool
    {
        return $this->relation === $other->relation && $this->field === $other->field && $this->destinationField === $other->destinationField;
    }

    public static function equalsPredicate(RelationToFetch $other): Closure
    {
        return fn(RelationToFetch $relationToFetch) => $relationToFetch->equals($other);
    }
}
