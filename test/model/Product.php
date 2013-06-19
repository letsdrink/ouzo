<?php
namespace Model;

use Thulium\Model;

class Product extends Model
{
    private $_fields = array('description', 'name', 'id_category');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'table' => 'products',
            'sequence' => 'products_id_product_seq',
            'primaryKey' => 'id_product',
            'attributes' => $attributes,
            'fields' => $this->_fields));
    }

    public function validate()
    {
        $this->_errors = array();
        if (!$this->name ) {
            $this->_errors[] = 'Empty name';
            $this->_errorFields[] = 'name';
        }
    }
}