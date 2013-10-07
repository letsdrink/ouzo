<?php

namespace Ouzo\Db;

use Ouzo\Utilities\Arrays;

class BelongsToRelation extends Relation
{
    function __construct($name, array $params, $primaryKey)
    {
        parent::__construct($name, $params);

        $this->referencedColumn = isset($params['referencedColumn'])? $params['referencedColumn'] : $this->getRelationModelObject()->getIdName();
        $this->collection = false;
    }
}