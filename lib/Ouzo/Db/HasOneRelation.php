<?php

namespace Ouzo\Db;


use Ouzo\Utilities\Arrays;

class HasOneRelation extends Relation
{
    function __construct($name, array $params)
    {
        parent::__construct($name, $params);
        $this->validateNotEmpty($params, 'foreignKey');
        $this->foreignKey = $params['foreignKey'];
    }
}