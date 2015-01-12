<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Application\Model\Test;

use Ouzo\Model;

/**
 * @property string description
 * @property string name
 * @property Category category
 */
class ModelWithoutPrimaryKey extends Model
{
    private $_fields = array('name');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'table' => 'products',
            'primaryKey' => '',
            'attributes' => $attributes,
            'fields' => $this->_fields));
    }
}
