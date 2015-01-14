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
 */
class Category extends Model
{
    //id is not required here but it should not cause errors (it's here just for a test)
    private $_fields = array('id', 'name', 'id_parent');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'hasMany' => array(
                'products' => array('class' => 'Test\Product', 'foreignKey' => 'id_category'),
                'products_starting_with_b' => array(
                    'class' => 'Test\Product',
                    'foreignKey' => 'id_category',
                    'conditions' => "products.name LIKE 'b%'",
                ),
                'products_ending_with_b_or_y' => array(
                    'class' => 'Test\Product',
                    'foreignKey' => 'id_category',
                    'conditions' => function () {
                        return WhereClause::create("products.name LIKE ? OR products.name LIKE ?", array('%b', '%y'));
                    },
                ),
                'products_name_bob' => array(
                    'class' => 'Test\Product',
                    'foreignKey' => 'id_category',
                    'conditions' => array("products.name" => "bob")
                )
            ),
            'hasOne' => array(
                'product_named_billy' => array(
                    'class' => 'Test\Product',
                    'foreignKey' => 'id_category',
                    'conditions' => "products.name = 'billy'"
                )
            ),
            'belongsTo' => array('parent' => array('class' => 'Test\Category', 'foreignKey' => 'id_parent', 'referencedColumn' => 'id')),
            'attributes' => $attributes,
            'fields' => $this->_fields));
    }

    public function getName($name)
    {
        return Db::callFunction('get_name', array($name));
    }
}
