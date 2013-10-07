<?php

namespace Ouzo\Db;

use Ouzo\Utilities\Arrays;

class HasManyRelation extends Relation
{
    function __construct($name, array $params, $primaryKey)
    {
        parent::__construct($name, $params);

        $this->referencedColumn = Arrays::getValue($params, 'referencedColumn', $primaryKey);
        $this->collection = true;
    }
}