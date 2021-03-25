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
    public function __construct(array $attributes = [])
    {
        parent::__construct([
            'table' => 'products',
            'primaryKey' => '',
            'attributes' => $attributes,
            'fields' => ['name']
        ]);
    }
}
