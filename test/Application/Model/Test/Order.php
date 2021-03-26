<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Application\Model\Test;

use Ouzo\Model;

class Order extends Model
{
    public function __construct($attributes = [])
    {
        parent::__construct([
            'table' => 'orders',
            'sequence' => 'orders_id_order_seq',
            'primaryKey' => 'id_order',
            'attributes' => $attributes,
            'fields' => ['name']
        ]);
    }
}
