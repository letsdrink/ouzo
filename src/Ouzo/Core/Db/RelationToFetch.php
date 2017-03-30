<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class RelationToFetch
{
    /** @var string */
    public $field;
    /** @var string */
    public $relation;
    /** @var string */
    public $destinationField;

    /**
     * @param string $field
     * @param string $relation
     * @param string $destinationField
     */
    public function __construct($field, $relation, $destinationField)
    {
        $this->field = $field;
        $this->relation = $relation;
        $this->destinationField = $destinationField;
    }

    /**
     * @param RelationToFetch $other
     * @return bool
     */
    public function equals(RelationToFetch $other)
    {
        return $this->relation === $other->relation && $this->field === $other->field && $this->destinationField === $other->destinationField;
    }

    /**
     * @param RelationToFetch $other
     * @return \Closure
     */
    public static function equalsPredicate(RelationToFetch $other)
    {
        return function (RelationToFetch $relationToFetch) use ($other) {
            return $relationToFetch->equals($other);
        };
    }
}
