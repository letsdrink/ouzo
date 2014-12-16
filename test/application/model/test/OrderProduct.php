<?php
namespace Application\Model\Test;

use Ouzo\Model;

class OrderProduct extends Model
{
    private $_fields = array('id_order', 'id_product');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'table' => 'order_products',
            'sequence' => '',
            'primaryKey' => '',
            'attributes' => $attributes,
            'belongsTo' => array(
                'product' => array('class' => 'Test\Product', 'foreignKey' => 'id_product'),
                'order' => array('class' => 'Test\Order', 'foreignKey' => 'id_order')
            ),
            'fields' => $this->_fields));
    }

}