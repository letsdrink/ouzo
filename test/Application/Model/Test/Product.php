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
    public function __construct(array $attributes = [])
    {
        parent::__construct([
            'attributes' => $attributes,
            'belongsTo' => [
                'manufacturer' => ['class' => 'Test\Manufacturer', 'foreignKey' => 'id_manufacturer'],
                'category' => ['class' => 'Test\Category', 'foreignKey' => 'id_category'],
                'categoryWithNameByDescription' => ['class' => 'Test\Category', 'foreignKey' => 'description', 'referencedColumn' => 'name']
            ],
            'hasOne' => ['orderProduct' => ['class' => 'Test\OrderProduct', 'foreignKey' => 'id_product']],
            'fields' => ['description', 'name', 'id_category', 'id_manufacturer', 'sale']
        ]);
    }

    public function validate(): void
    {
        parent::validate();
        if (!$this->name) {
            parent::error('Empty name');
            $this->errorFields[] = 'name';
        }
    }

    public function getDescription(): string
    {
        return 'This is product,';
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
