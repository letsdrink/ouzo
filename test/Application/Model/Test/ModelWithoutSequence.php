<?php

namespace Application\Model\Test;


use Ouzo\Model;

class ModelWithoutSequence extends Model
{
    function __construct($attributes = array())
    {
        parent::__construct(array(
            'table' => 'products',
            'primaryKey' => 'id',
            'fields' => array('name'),
            'sequence' => '',
            'attributes' => $attributes
        ));
    }
}