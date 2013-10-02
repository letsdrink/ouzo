<?php
namespace Model;

use Ouzo\Db;
use Ouzo\Model;

class Category extends Model
{
    private $_fields = array('name');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'primaryKey' => 'id_category',
            'attributes' => $attributes,
            'fields' => $this->_fields));
    }

    public function getName($name)
    {
        return Db::callFunction('get_name', array($name));
    }
}