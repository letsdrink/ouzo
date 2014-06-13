<?php
namespace Ouzo\Db;

class RelationToFetch
{
    public $field;
    public $relation;
    public $destinationField;

    public function __construct($field, $relation, $destinationField)
    {
        $this->field = $field;
        $this->relation = $relation;
        $this->destinationField = $destinationField;
    }

    public function equals(RelationToFetch $other)
    {
        return $this->relation === $other->relation && $this->field === $other->field && $this->destinationField === $other->destinationField;
    }

    public static function equalsPredicate($other)
    {
        return function ($relationToFetch) use($other) {
            return $relationToFetch->equals($other);
        };
    }
}