<?php
namespace Model\Test;

use Ouzo\Model;

class Product extends Model
{
    private $_fields = array('description', 'name', 'id_category', 'sale');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'table' => 'products',
            'sequence' => 'products_id_product_seq',
            'primaryKey' => 'id_product',
            'attributes' => $attributes,
            'belongsTo' => array(
                'category' => array('class' => 'Test\Category', 'foreignKey' => 'id_category'),
                'categoryWithNameByDescription' => array('class' => 'Test\Category', 'foreignKey' => 'description', 'referencedColumn' => 'name'),
                'categoryWithNameByDescriptionAllowInvalid' => array('class' => 'Test\Category', 'foreignKey' => 'description', 'referencedColumn' => 'name', 'allowInvalidReferences' => true)
            ),
            'hasOne' => array('orderProduct' => array('class' => 'Test\OrderProduct', 'foreignKey' => 'id_product')),
            'fields' => $this->_fields));
    }

    public function validate()
    {
        parent::validate();
        if (!$this->name) {
            $this->_errors[] = 'Empty name';
            $this->_errorFields[] = 'name';
        }
    }

    public function getDescription()
    {
        return 'This is product,';
    }
}