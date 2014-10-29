<?php
namespace Model\Test;

use Ouzo\Model;


/**
 * @property string description
 * @property string name
 * @property Category category
 * @property Order[] orders
 */
class Product extends Model
{
    private $_fields = array('description', 'name', 'id_category', 'id_manufacturer', 'sale');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'attributes' => $attributes,
            'belongsTo' => array(
                'manufacturer' => array('class' => 'Test\Manufacturer', 'foreignKey' => 'id_manufacturer'),
                'category' => array('class' => 'Test\Category', 'foreignKey' => 'id_category'),
                'categoryWithNameByDescription' => array('class' => 'Test\Category', 'foreignKey' => 'description', 'referencedColumn' => 'name')
            ),
            'hasOne' => array('orderProduct' => array('class' => 'Test\OrderProduct', 'foreignKey' => 'id_product')),
            'hasMany' => array(
                'orderProducts' => array('class' => 'Test\OrderProduct', 'foreignKey' => 'id_product'),
                'orders' => array('through' => 'orderProducts', 'field' => 'order', 'class' => 'Test\Order')
            ),
            'fields' => $this->_fields));
    }

    public function validate()
    {
        parent::validate();
        if (!$this->name) {
            parent::error('Empty name');
            $this->_errorFields[] = 'name';
        }
    }

    public function getDescription()
    {
        return 'This is product,';
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function addOrder($order)
    {
        OrderProduct::create(array('id_order' => $order->getId(), 'id_product' => $this->getId()));
    }
}