<?php
namespace Ouzo\Db;

class RelationToFetch
{
    public $field;
    public $relation;
    public $nestedField;

    public function __construct($field, $relation, $nestedField)
    {
        $this->field = $field;
        $this->relation = $relation;
        $this->nestedField = $nestedField;
    }

    public function equals(RelationToFetch $other)
    {
        return $this->relation === $other->relation && $this->field === $other->field && $this->nestedField === $other->nestedField;
    }

    public static function equalsPredicate($other)
    {
        return function ($relationToFetch) use($other) {
            return $relationToFetch->equals($other);
        };
    }
}