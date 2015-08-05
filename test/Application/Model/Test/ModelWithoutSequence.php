<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Application\Model\Test;

use Ouzo\Model;

class ModelWithoutSequence extends Model
{
    public function __construct($attributes = array())
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
