<?php

namespace Ouzo\Db;


use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

class JoinTableRelation extends Relation
{
    private $through;
    private $field;

    public function __construct($name, $class, $through, $field)
    {
        parent::__construct($name, $class, null, null, null, null);
        $this->through = $through;
        $this->field = $field;
    }

    public function getResultTransformer()
    {
        return new JoinTableRelationFetcher($this);
    }

    public function getJoinModelField()
    {
        return $this->through;
    }

    public function getJoinField()
    {
        return $this->field;
    }

    public function extractValue($values)
    {
        return Arrays::map($values, Functions::extractField($this->getJoinField()));
    }

    public function getNestedRelations($model)
    {
        $relations = ModelQueryBuilderHelper::extractRelations($model, "{$this->through}->{$this->field}");
        return $relations;
    }

}