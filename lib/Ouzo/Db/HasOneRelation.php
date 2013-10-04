<?php

namespace Ouzo\Db;


use Ouzo\Utilities\Arrays;

class HasOneRelation extends Relation
{
    function __construct($name, array $params, $primaryKey)
    {
        parent::__construct($name, $params);
        $this->validateNotEmpty($params, 'foreignKey');
        $this->foreignKey = $params['foreignKey'];
        $this->referencedColumn = Arrays::getValue($params, 'referencedColumn', $primaryKey);
    }
}