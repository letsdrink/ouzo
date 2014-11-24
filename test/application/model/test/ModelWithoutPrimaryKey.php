<?php
namespace Application\Model\Test;

use Ouzo\Model;


/**
 * @property string description
 * @property string name
 * @property Category category
 */
class ModelWithoutPrimaryKey extends Model
{
    private $_fields = array('name');

    public function __construct($attributes = array())
    {
        parent::__construct(array(
            'table' => 'products',
            'primaryKey' => '',
            'attributes' => $attributes,
            'fields' => $this->_fields));
    }
}