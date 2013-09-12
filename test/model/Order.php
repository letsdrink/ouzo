<?php

namespace Model;

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