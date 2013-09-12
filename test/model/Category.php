<?php

namespace Model;

use Ouzo\Model;

class Category extends Model
{
    private $_fields = array('name');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'table' => 'categories',
            'sequence' => 'categories_id_category_seq',
            'primaryKey' => 'id_category',
            'attributes' => $attributes,
            'fields' => $this->_fields));
    }

}