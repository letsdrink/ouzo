<?php

namespace Ouzo\Db;

use Ouzo\Utilities\Arrays;

class BelongsToRelation extends Relation
{
    function __construct($name, array $params, $primaryKey)
    {
        parent::__construct($name, $params);
        $this->validateNotEmpty($params, 'foreignKey');
        $this->foreignKey = Arrays::getValue($params, 'foreignKey');
        $this->referencedColumn = Arrays::getValue($params, 'referencedColumn', $primaryKey);
    }
}