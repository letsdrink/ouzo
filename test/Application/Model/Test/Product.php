<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Application\Model\Test;

use Ouzo\Model;

/**
 * @property int id
 * @property string description
 * @property string name
 * @property Category category
 */
class Product extends Model
{
    private $_fields = ['description', 'name', 'id_category', 'id_manufacturer', 'sale'];

    public function __construct($attributes = [])
    {
        parent::__construct([
            'attributes' => $attributes,
            'belongsTo' => [
                'manufacturer' => ['class' => 'Test\Manufacturer', 'foreignKey' => 'id_manufacturer'],
                'category' => ['class' => 'Test\Category', 'foreignKey' => 'id_category'],
                'categoryWithNameByDescription' => ['class' => 'Test\Category', 'foreignKey' => 'description', 'referencedColumn' => 'name']
            ],
            'hasOne' => ['orderProduct' => ['class' => 'Test\OrderProduct', 'foreignKey' => 'id_product']],
            'fields' => $this->_fields]);
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
}
