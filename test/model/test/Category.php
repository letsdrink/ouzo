<?php
namespace Model\Test;

use Ouzo\Db;
use Ouzo\Model;

class Category extends Model
{
    //id is not required here but it should not cause errors (it's here just for a test)
    private $_fields = array('id', 'name', 'id_parent');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'hasMany' => array('products' => array('class' => 'Test\Product', 'foreignKey' => 'id_category')),
            'belongsTo' => array('parent' => array('class' => 'Test\Category', 'foreignKey' => 'id_parent', 'referencedColumn' => 'id')),
            'attributes' => $attributes,
            'fields' => $this->_fields));
    }

    public function getName($name)
    {
        return Db::callFunction('get_name', array($name));
    }
}