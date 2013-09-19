<?php
namespace Model;

use Ouzo\Model;

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
        parent::validate();
        if (!$this->name ) {
            $this->_errors[] = 'Empty name';
            $this->_errorFields[] = 'name';
        }
    }

    public function getDescription() {
        return 'This is product,';
    }
}