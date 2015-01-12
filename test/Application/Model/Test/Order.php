<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Application\Model\Test;

use Ouzo\Model;

class Order extends Model
{
    private $_fields = array('name');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'table' => 'orders',
            'sequence' => 'orders_id_order_seq',
            'primaryKey' => 'id_order',
            'attributes' => $attributes,
            'fields' => $this->_fields));
    }
}
