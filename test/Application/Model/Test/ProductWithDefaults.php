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
class ProductWithDefaults extends Model
{
    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'table' => 'products',
            'attributes' => $attributes,
            'fields' => array(
                'description' => 'no desc',
                'name' => function () {
                    return 'no name';
                },
                'id_category',
                'id_manufacturer',
                'sale'
            )));
    }
}
