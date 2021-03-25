<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Application\Model\Test;

use Ouzo\Db;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Model;

/**
 * @property int id
 * @property string name
 * @property Category parent
 * @property Product[] products_ordered_by_name
 */
class Category extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct([
            'hasMany' => [
                'products' => ['class' => 'Test\Product', 'foreignKey' => 'id_category'],
                'products_starting_with_b' => [
                    'class' => 'Test\Product',
                    'foreignKey' => 'id_category',
                    'conditions' => "products.name LIKE 'b%'",
                ],
                'products_ending_with_b_or_y' => [
                    'class' => 'Test\Product',
                    'foreignKey' => 'id_category',
                    'conditions' => fn() => WhereClause::create('products.name LIKE ? OR products.name LIKE ?', ['%b', '%y']),
                ],
                'products_name_bob' => [
                    'class' => 'Test\Product',
                    'foreignKey' => 'id_category',
                    'conditions' => ["products.name" => "bob"]
                ],
                'products_ordered_by_name' => [
                    'class' => 'Test\Product',
                    'foreignKey' => 'id_category',
                    'order' => ["products.name ASC"]
                ]
            ],
            'hasOne' => [
                'product_named_billy' => [
                    'class' => 'Test\Product',
                    'foreignKey' => 'id_category',
                    'conditions' => "products.name = 'billy'"
                ]
            ],
            'belongsTo' => ['parent' => ['class' => 'Test\Category', 'foreignKey' => 'id_parent', 'referencedColumn' => 'id']],
            'attributes' => $attributes,
            //id is not required here but it should not cause errors (it's here just for a test)
            'fields' => ['id', 'name', 'id_parent']
        ]);
    }

    public function getName(string $name): string
    {
        return Db::callFunction('get_name', [$name]);
    }
}
