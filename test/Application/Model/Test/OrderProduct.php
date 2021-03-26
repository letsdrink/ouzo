<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Application\Model\Test;

use Ouzo\Model;

class OrderProduct extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct([
            'table' => 'order_products',
            'sequence' => '',
            'primaryKey' => '',
            'attributes' => $attributes,
            'belongsTo' => [
                'product' => ['class' => 'Test\Product', 'foreignKey' => 'id_product'],
                'order' => ['class' => 'Test\Order', 'foreignKey' => 'id_order']
            ],
            'fields' => ['id_order', 'id_product']
        ]);
    }
}
