<?php
namespace Ouzo\Db;

class RelationToFetch
{
    public $field;
    public $relation;

    public function __construct($field, $relation)
    {
        $this->field = $field;
        $this->relation = $relation;
    }

    public function equals(RelationToFetch $other)
    {
        return $this->relation === $other->relation && $this->field === $other->field;
    }

    public static function equalsPredicate($other)
    {
        return function ($relationToFetch) use($other) {
            return $relationToFetch->equals($other);
        };
    }
}