<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Application\Model\Test;

use Ouzo\Model;

/**
 * @property int id
 * @property string name
 * @property Product[] products
 */
class Manufacturer extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct([
            'hasMany' => ['products' => ['class' => 'Test\Product', 'foreignKey' => 'id_manufacturer']],
            'attributes' => $attributes,
            'fields' => ['name']
        ]);
    }
}
